<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Loaders;

use Wizaplace\Etl\Database\Manager;
use Wizaplace\Etl\Database\Transaction;
use Wizaplace\Etl\Row;

class InsertUpdate extends Loader
{
    /**
     * The connection name.
     */
    protected string $connection = 'default';

    /**
     * The primary key.
     *
     * @var mixed
     */
    protected $key = ['id'];

    /**
     * The columns to insert/update.
     *
     * @var string[]
     */
    protected array $columns = [];

    /**
     * Indicates if existing destination rows in table should be updated.
     */
    protected bool $doUpdates = true;

    /**
     * Indicates if the table has timestamps columns.
     */
    protected bool $timestamps = false;

    /**
     * Indicates if the loader will perform transactions.
     */
    protected bool $transaction = true;

    /**
     * Transaction commit size.
     */
    protected int $commitSize = 100;

    /**
     * Time for timestamps columns.
     */
    protected string $time;

    /**
     * The select statement.
     *
     * @var \PDOStatement|false|null
     */
    protected $select = null;

    /**
     * The insert statement.
     *
     * @var \PDOStatement|false|null
     */
    protected $insert = null;

    /**
     * The update statement.
     *
     * @var \PDOStatement|false|null
     */
    protected $update = null;

    /**
     * The database transaction manager.
     */
    protected Transaction $transactionManager;

    /**
     * The database manager.
     */
    protected Manager $db;

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected array $availableOptions = [
        'columns', 'connection', 'key', 'timestamps', 'transaction', 'commitSize', 'doUpdates',
    ];

    /**
     * Create a new InsertUpdate Loader instance.
     */
    public function __construct(Manager $manager)
    {
        $this->db = $manager;
    }

    public function initialize(): void
    {
        if ($this->timestamps) {
            $this->time = date('Y-m-d G:i:s');
        }

        if ($this->transaction) {
            $this->transactionManager = $this->db->transaction($this->connection)->size($this->commitSize);
        }

        if ([] !== $this->columns && array_keys($this->columns) === range(0, count($this->columns) - 1)) {
            $this->columns = array_combine($this->columns, $this->columns);
        }
    }

    /**
     * Load the given row.
     */
    public function load(Row $row): void
    {
        $row = $row->toArray();

        if ($this->transaction) {
            $this->transactionManager->run(function () use ($row): void {
                $this->execute($row);
            });
        } else {
            $this->execute($row);
        }
    }

    /**
     * Finalize the step.
     */
    public function finalize(): void
    {
        if ($this->transaction) {
            $this->transactionManager->close();
        }
    }

    /**
     * Prepare the select statement.
     */
    protected function prepareSelect(): void
    {
        $this->select = $this->db->statement($this->connection)->select($this->output)->where($this->key)->prepare();
    }

    /**
     * Prepare the insert statement.
     *
     * @param string[] $sample
     */
    protected function prepareInsert(array $sample): void
    {
        if ([] !== $this->columns) {
            $columns = array_values($this->columns);
        } else {
            $columns = array_keys($sample);
        }

        if ($this->timestamps) {
            array_push($columns, 'created_at', 'updated_at');
        }

        $this->insert = $this->db->statement($this->connection)->insert($this->output, $columns)->prepare();
    }

    /**
     * Prepare the update statement.
     *
     * @param string[] $sample
     */
    protected function prepareUpdate(array $sample): void
    {
        if ([] !== $this->columns) {
            $columns = array_values(array_diff($this->columns, $this->key));
        } else {
            $columns = array_keys(array_diff_key($sample, array_flip($this->key)));
        }

        if ($this->timestamps) {
            array_push($columns, 'updated_at');
        }

        $this->update = $this->db->statement($this->connection)
            ->update($this->output, $columns)
            ->where($this->key)
            ->prepare();
    }

    /**
     * Execute the given row.
     */
    protected function execute(array $row): void
    {
        if (null === $this->select) {
            $this->prepareSelect();
        }

        if ([] !== $this->columns) {
            $mappedColumnsArr = [];
            $keyColumns = array_intersect($this->columns, $this->key);

            foreach ($keyColumns as $key => $column) {
                $mappedColumnsArr[$column] = array_intersect_key($row, $keyColumns)[$key];
            }
            $this->select->execute($mappedColumnsArr);
        } else {
            $this->select->execute(array_intersect_key($row, array_flip($this->key)));
        }

        if ([] !== $this->columns) {
            $result = [];

            foreach ($this->columns as $key => $column) {
                isset($row[$key]) ? $result[$column] = $row[$key] : $result[$column] = null;
            }

            $row = $result;
        }

        $current = $this->select->fetch();
        if (false === $current) {
            $this->insert($row);
        } else {
            $this->update($row, $current);
        }
    }

    /**
     * Execute the insert statement.
     */
    protected function insert(array $row): void
    {
        if (null === $this->insert) {
            $this->prepareInsert($row);
        }

        if ($this->timestamps) {
            $row['created_at'] = $this->time;
            $row['updated_at'] = $this->time;
        }

        $this->insert->execute($row);
    }

    /**
     * Execute the update statement.
     */
    protected function update(array $row, array $current): void
    {
        if (false === $this->doUpdates) {
            return;
        }

        if (null === $this->update) {
            $this->prepareUpdate($row);
        }

        if ($row == array_intersect_key($current, $row)) {
            return;
        }

        if ($this->timestamps) {
            $row['updated_at'] = $this->time;
        }

        $this->update->execute($row);
    }
}

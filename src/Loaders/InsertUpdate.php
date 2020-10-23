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
use Wizaplace\Etl\Row;

class InsertUpdate extends Loader
{
    /**
     * The connection name.
     *
     * @var string
     */
    protected $connection = 'default';

    /**
     * The primary key.
     *
     * @var mixed
     */
    protected $key = ['id'];

    /**
     * The columns to insert/update.
     *
     * @var string[]|null
     */
    protected $columns;

    /**
     * Indicates if existing destination rows in table should be updated.
     *
     * @var bool
     */
    protected $doUpdates = true;

    /**
     * Indicates if the table has timestamps columns.
     *
     * @var bool
     */
    protected $timestamps = false;

    /**
     * Indicates if the loader will perform transactions.
     *
     * @var bool
     */
    protected $transaction = true;

    /**
     * Transaction commit size.
     *
     * @var int
     */
    protected $commitSize = 100;

    /**
     * Time for timestamps columns.
     *
     * @var string
     */
    protected $time;

    /**
     * The select statement.
     *
     * @var \PDOStatement
     */
    protected $select;

    /**
     * The insert statement.
     *
     * @var \PDOStatement
     */
    protected $insert;

    /**
     * The update statement.
     *
     * @var \PDOStatement
     */
    protected $update;

    /**
     * The database transaction manager.
     *
     * @var \Wizaplace\Etl\Database\Transaction
     */
    protected $transactionManager;

    /**
     * The database manager.
     *
     * @var \Wizaplace\Etl\Database\Manager
     */
    protected $db;

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected $availableOptions = [
        'columns', 'connection', 'key', 'timestamps', 'transaction', 'commitSize', 'doUpdates',
    ];

    /**
     * Create a new InsertUpdate Loader instance.
     *
     * @return void
     */
    public function __construct(Manager $manager)
    {
        $this->db = $manager;
    }

    /**
     * Initialize the step.
     */
    public function initialize(): void
    {
        if ($this->timestamps) {
            $this->time = date('Y-m-d G:i:s');
        }

        if ($this->transaction) {
            $this->transactionManager = $this->db->transaction($this->connection)->size($this->commitSize);
        }

        if (
            is_array($this->columns) && [] !== $this->columns
            && array_keys($this->columns) === range(0, count($this->columns) - 1)
        ) {
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
        if (is_array($this->columns) ?? [] !== $this->columns) {
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
        if (is_array($this->columns) ?? [] !== $this->columns) {
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
     *
     * @param array $row
     */
    protected function execute(array $row): void
    {
        if (null === $this->select) {
            $this->prepareSelect();
        }

        if (is_array($this->columns) ?? [] !== $this->columns) {
            $mapped_columns_arr = [];
            $key_columns = array_intersect($this->columns, $this->key);

            foreach ($key_columns as $key => $column) {
                $mapped_columns_arr[$column] = array_intersect_key($row, $key_columns)[$key];
            }
            $this->select->execute($mapped_columns_arr);
        } else {
            $this->select->execute(array_intersect_key($row, array_flip($this->key)));
        }

        if (is_array($this->columns) ?? [] !== $this->columns) {
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

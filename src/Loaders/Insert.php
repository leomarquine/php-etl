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

class Insert extends Loader
{
    /**
     * The connection name.
     */
    protected string $connection = 'default';

    /**
     * The columns to insert.
     *
     * @var string[]
     */
    protected array $columns = [];

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
    protected int $commitSize = 0;

    /**
     * Time for timestamps columns.
     */
    protected string $time;

    /**
     * The insert statement.
     */
    protected \PDOStatement $insert;

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
    protected array $availableOptions = ['columns', 'connection', 'timestamps', 'transaction', 'commitSize'];

    /**
     * Create a new Insert Loader instance.
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
                $this->insert($row);
            });
        } else {
            $this->insert($row);
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
     * Prepare the insert statement.
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
     * Execute the insert query.
     */
    protected function insert(array $row): void
    {
        if (!isset($this->insert)) {
            $this->prepareInsert($row);
        }

        if ([] !== $this->columns) {
            $result = [];

            foreach ($this->columns as $key => $column) {
                isset($row[$key]) ? $result[$column] = $row[$key] : $result[$column] = null;
            }

            $row = $result;
        }

        if ($this->timestamps) {
            $row['created_at'] = $this->time;
            $row['updated_at'] = $this->time;
        }

        $this->insert->execute($row);
    }
}

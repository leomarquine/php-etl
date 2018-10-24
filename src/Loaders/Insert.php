<?php

namespace Marquine\Etl\Loaders;

use Marquine\Etl\Database\Manager;

class Insert extends Loader
{
    /**
     * The connection name.
     *
     * @var string
     */
    protected $connection = 'default';

    /**
     * The columns to insert.
     *
     * @var array
     */
    protected $columns;

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
     * The insert statement.
     *
     * @var \PDOStatement
     */
    protected $insert;

    /**
     * The database manager.
     *
     * @var \Marquine\Etl\Database\Manager
     */
    protected $db;

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns', 'connection', 'timestamps', 'transaction', 'commitSize'
    ];

    /**
     * Create a new Insert Loader instance.
     *
     * @param  \Marquine\Etl\Database\Manager  $manager
     * @return void
     */
    public function __construct(Manager $manager)
    {
        $this->db = $manager;
    }

    /**
     * Get the loader handler.
     *
     * @param  mixed  $destination
     * @return callable
     */
    public function load($destination)
    {
        $this->prepareInsert($destination, $this->pipeline->sample());

        if ($this->timestamps) {
            $this->time = date('Y-m-d G:i:s');
        }

        if (! empty($this->columns) && array_keys($this->columns) === range(0, count($this->columns) -1)) {
            $this->columns = array_combine($this->columns, $this->columns);
        }

        $transaction = $this->transaction ? $this->db->transaction($this->connection)->size($this->commitSize) : null;

        return function ($row) use ($transaction) {
            if ($transaction) {
                $transaction->run($this->pipeline->metadata(), function () use ($row) {
                    $this->insert($row);
                });
            } else {
                $this->insert($row);
            }

            return $row;
        };
    }

    /**
     * Prepare the insert statement.
     *
     * @param  string  $table
     * @param  array  $sample
     * @return void
     */
    protected function prepareInsert($table, $sample)
    {
        if ($this->columns) {
            $columns = array_values($this->columns);
        } else {
            $columns = array_keys($sample);
        }

        if ($this->timestamps) {
            array_push($columns, 'created_at', 'updated_at');
        }

        $this->insert = $this->db->statement($this->connection)->insert($table, $columns)->prepare();
    }

    /**
     * Execute the insert query.
     *
     * @param  array  $row
     * @return void
     */
    protected function insert($row)
    {
        if ($this->columns) {
            $result = [];

            foreach ($this->columns as $key => $column) {
                $result[$column] = $row[$key];
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

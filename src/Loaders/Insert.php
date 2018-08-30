<?php

namespace Marquine\Etl\Loaders;

use Marquine\Etl\Pipeline;
use Marquine\Etl\Database\Manager;

class Insert implements LoaderInterface
{
    /**
     * The connection name.
     *
     * @var string
     */
    public $connection = 'default';

    /**
     * The columns to insert.
     *
     * @var array
     */
    public $columns;

    /**
     * Indicates if the table has timestamps columns.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Indicates if the loader will perform transactions.
     *
     * @var bool
     */
    public $transaction = true;

    /**
     * Transaction commit size.
     *
     * @var int
     */
    public $commitSize = 100;

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
     * @param  \Marquine\Etl\Pipeline  $pipeline
     * @param  string  $destination
     * @return callable
     */
    public function handler(Pipeline $pipeline, $destination)
    {
        $this->prepareInsert($destination, $pipeline->sample());

        if ($this->timestamps) {
            $this->time = date('Y-m-d G:i:s');
        }

        if (! empty($this->columns) && array_keys($this->columns) === range(0, count($this->columns) -1)) {
            $this->columns = array_combine($this->columns, $this->columns);
        }

        $transaction = $this->transaction ? $this->db->transaction($this->connection)->size($this->commitSize) : null;

        return function ($row, $metadata) use ($transaction) {
            if ($transaction) {
                $transaction->run($metadata, function () use ($row) {
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

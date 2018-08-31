<?php

namespace Marquine\Etl\Loaders;

use Marquine\Etl\Pipeline;
use Marquine\Etl\Database\Manager;

class InsertUpdate implements LoaderInterface
{
    /**
     * The connection name.
     *
     * @var string
     */
    public $connection = 'default';

    /**
     * The primary key.
     *
     * @var mixed
     */
    public $key = ['id'];

    /**
     * The columns to insert/update.
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
     * The database manager.
     *
     * @var \Marquine\Etl\Database\Manager
     */
    protected $db;

    /**
     * Create a new InsertUpdate Loader instance.
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
        if ($this->timestamps) {
            $this->time = date('Y-m-d G:i:s');
        }

        if (!empty($this->columns) && array_keys($this->columns) === range(0, count($this->columns) - 1)) {
            $this->columns = array_combine($this->columns, $this->columns);
        }

        $this->prepareStatements($destination, $pipeline->sample());

        $transaction = $this->transaction ? $this->db->transaction($this->connection)->size($this->commitSize) : null;

        return function ($row, $metadata) use ($transaction) {
            if ($transaction) {
                $transaction->run($metadata, function () use ($row) {
                    $this->execute($row);
                });
            } else {
                $this->execute($row);
            }

            return $row;
        };
    }

    /**
     * Prepare the loader statements.
     *
     * @param  string  $table
     * @param  array   $sample
     * @return void
     */
    protected function prepareStatements($table, $sample)
    {
        $this->prepareSelect($table);
        $this->prepareInsert($table, $sample);
        $this->prepareUpdate($table, $sample);
    }

    /**
     * Prepare the select statement.
     *
     * @param  string  $table
     * @return void
     */
    protected function prepareSelect($table)
    {
        $this->select = $this->db->statement($this->connection)->select($table)->where($this->key)->prepare();
    }

    /**
     * Prepare the insert statement.
     *
     * @param  string  $table
     * @param  array   $sample
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
     * Prepare the update statement.
     *
     * @param  string  $table
     * @param  array   $sample
     * @return void
     */
    protected function prepareUpdate($table, $sample)
    {
        if ($this->columns) {
            $columns = array_values(array_diff($this->columns, $this->key));
        } else {
            $columns = array_keys(array_diff_key($sample, array_flip($this->key)));
        }

        if ($this->timestamps) {
            array_push($columns, 'updated_at');
        }

        $this->update = $this->db->statement($this->connection)->update($table, $columns)->where($this->key)->prepare();
    }

    /**
     * Execute the given row.
     *
     * @param  array  $row
     * @return void
     */
    protected function execute($row)
    {
        $this->select->execute(array_intersect_key($row, array_flip($this->key)));

        if ($this->columns) {
            $result = [];

            foreach ($this->columns as $key => $column) {
                $result[$column] = $row[$key];
            }

            $row = $result;
        }

        if ($current = $this->select->fetch()) {
            $this->update($row, $current);
        } else {
            $this->insert($row);
        }
    }

    /**
     * Execute the insert statement.
     *
     * @param  array  $row
     * @return void
     */
    protected function insert($row)
    {
        if ($this->timestamps) {
            $row['created_at'] = $this->time;
            $row['updated_at'] = $this->time;
        }

        $this->insert->execute($row);
    }

    /**
     * Execute the update statement.
     *
     * @param  array  $row
     * @param  array  $current
     * @return void
     */
    protected function update($row, $current)
    {
        if ($row == array_intersect_key($current, $row)) {
            return;
        }

        if ($this->timestamps) {
            $row['updated_at'] = $this->time;
        }

        $this->update->execute($row);
    }
}

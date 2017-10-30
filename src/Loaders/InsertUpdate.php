<?php

namespace Marquine\Etl\Loaders;

use Generator;
use Marquine\Etl\Database\Statement;
use Marquine\Etl\Database\Transaction;

class InsertUpdate extends Loader
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
    public $columns = [];

    /**
     * Indicates if the table has timestamps columns.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Transaction mode.
     *
     * @var mixed
     */
    public $transaction = 'single';

    /**
     * The database table.
     *
     * @var string
     */
    protected $table;

    /**
     * Timestamps columns value.
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
     * Load data into the given destination.
     *
     * @param  \Generator  $data
     * @param  string  $destination
     * @return void
     */
    public function load(Generator $data, $destination)
    {
        $this->normalizeColumns($data);

        $this->normalizeKey();

        $this->table = $destination;

        $this->time = date('Y-m-d G:i:s');

        Transaction::connection($this->connection)->mode($this->transaction)->data($data)->run(function ($row) {
            if ($this->exists($row)) {
                $this->update($row);
            } else {
                $this->insert($row);
            }
        });
    }

    /**
     * Verify if a row exists on the database.
     *
     * @param  array  $row
     * @return bool
     */
    protected function exists($row)
    {
        if (! $this->select) {
            $this->select = Statement::connection($this->connection)->select($this->table)->where($this->key)->prepare();
        }

        $this->select->execute(array_intersect_key($row, $this->key));

        return (bool) $this->select->fetch();
    }

    /**
     * Insert the row.
     *
     * @param  array  $row
     * @return void
     */
    protected function insert($row)
    {
        if (! $this->insert) {
            $this->insert = Statement::connection($this->connection)->insert($this->table, $this->getInsertColumns())->prepare();
        }

        $row = array_intersect_key($row, $this->columns + $this->key);

        if ($this->timestamps) {
            $row['created_at'] = $this->time;
            $row['updated_at'] = $this->time;
        }

        $this->insert->execute($row);
    }

    /**
     * Update the row.
     *
     * @param  array  $row
     * @return void
     */
    protected function update($row)
    {
        if (! $this->update) {
            $this->update = Statement::connection($this->connection)->update($this->table, $this->getUpdateColumns())->where($this->key)->prepare();
        }

        $row = array_intersect_key($row, $this->columns + $this->key);

        if ($this->timestamps) {
            $row['updated_at'] = $this->time;
        }

        $this->update->execute($row);
    }

    /**
     * Get the columns for the insert statement.
     *
     * @return array
     */
    protected function getInsertColumns()
    {
        $columns = array_values($this->columns);

        if ($this->timestamps) {
            $columns[] = 'created_at';
            $columns[] = 'updated_at';
        }

        return $columns;
    }

    /**
     * Get the columns for the update statement.
     *
     * @return void
     */
    protected function getUpdateColumns()
    {
        $columns = array_values(array_diff_key($this->columns, $this->key));

        if ($this->timestamps) {
            $columns[] = 'updated_at';
        }

        return $columns;
    }

    /**
     * Normalize the columns list.
     *
     * @param  \Generator  $data
     * @return void
     */
    protected function normalizeColumns($data)
    {
        if (empty($this->columns)) {
            $this->columns = array_keys($data->current());
        }

        $this->columns = array_combine($this->columns, $this->columns);
    }

    /**
     * Normalize the primary key.
     *
     * @return void
     */
    protected function normalizeKey()
    {
        if (is_string($this->key)) {
            $this->key = [$this->key];
        }

        $this->key = array_combine($this->key, $this->key);
    }
}

<?php

namespace Marquine\Etl\Loaders;

use Generator;
use Marquine\Etl\Database\Manager as DB;

class Insert extends Loader
{
    /**
     * The columns to insert.
     *
     * @var array
     */
    public $columns = [];

    /**
     * The connection name.
     *
     * @var string
     */
    public $connection = 'default';

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
     * The insert statement.
     *
     * @var \PDOStatement
     */
    protected $insert;

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
     * Load data into the given destination.
     *
     * @param  string  $destination
     * @param  \Generator  $data
     * @return void
     */
    public function load(Generator $data, $destination)
    {
        $this->normalizeColumns($data);

        $this->table = $destination;

        $this->time = date('Y-m-d G:i:s');

        DB::connection($this->connection)->transaction($this->transaction)->data($data)->run(function ($row) {
            $this->insert(array_intersect_key($row, $this->columns));
        });
    }

    /**
     * Execute the insert statement.
     *
     * @param  array  $row
     * @return void
     */
    protected function insert($row)
    {
        if (! $this->insert) {
            $this->insert = DB::connection($this->connection)->statement()->insert($this->table, $this->columns)->prepare();
        }

        if ($this->timestamps) {
            $row['created_at'] = $this->time;
            $row['updated_at'] = $this->time;
        }

        $this->insert->execute($row);
    }

    /**
     * Normalize columns.
     *
     * @param  \Generator  $data
     * @return void
     */
    protected function normalizeColumns($data)
    {
        if (empty($this->columns)) {
            $this->columns = array_keys($data->current());
        }

        if ($this->timestamps) {
            $this->columns[] = 'created_at';
            $this->columns[] = 'updated_at';
        }

        $this->columns = array_combine($this->columns, $this->columns);
    }
}

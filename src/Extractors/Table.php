<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Database\Query;

class Table implements ExtractorInterface
{
    /**
     * The connection name.
     *
     * @var string
     */
    public $connection = 'default';

    /**
     * Extractor columns.
     *
     * @var array
     */
    public $columns;

    /**
     * The array of where clause.
     *
     * @var array
     */
    public $where = [];

    /**
     * The database table.
     *
     * @var string
     */
    protected $table;

    /**
     * Set the extractor source.
     *
     * @param  mixed  $source
     * @return void
     */
    public function source($source)
    {
        $this->table = $source;
    }

    /**
     * Get the extractor iterator.
     *
     * @return \Generator
     */
    public function getIterator()
    {
        if (empty($this->columns)) {
            $this->columns = ['*'];
        }

        $statement = $this->query($this->connection)
            ->select($this->table, $this->columns)
            ->where($this->where)
            ->execute();

        while ($row = $statement->fetch()) {
            yield $row;
        }
    }

    /**
     * Get a database connection.
     *
     * @param  string  $connection
     * @return \Marquine\Etl\Database\Connection
     */
    protected function query($connection)
    {
        return Query::connection($connection);
    }

    /**
     * Extract data from the given source.
     *
     * @param  string  $table
     * @return \Generator
     */
    public function extract($table)
    {
        if (is_string($this->columns)) {
            $this->columns = [$this->columns];
        }

        if (empty($this->columns)) {
            $this->columns = ['*'];
        }

        $statement = Query::connection($this->connection)
                        ->select($table, $this->columns)
                        ->where($this->where)
                        ->execute();

        while ($row = $statement->fetch()) {
            yield $row;
        }
    }
}

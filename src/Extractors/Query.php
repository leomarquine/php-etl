<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Database\Manager;

class Query implements ExtractorInterface
{
    /**
     * The connection name.
     *
     * @var string
     */
    public $connection = 'default';

    /**
     * Query bindings.
     *
     * @var array
     */
    public $bindings = [];

    /**
     * SQL query statement.
     *
     * @var string
     */
    protected $query;

    /**
     * Set the extractor source.
     *
     * @param  mixed  $source
     * @return void
     */
    public function source($source)
    {
        $this->query = $source;
    }

    /**
     * Get the extractor iterator.
     *
     * @return \Generator
     */
    public function getIterator()
    {
        $statement = $this->db($this->connection)->prepare($this->query);

        $statement->execute($this->bindings);

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
    protected function db($connection)
    {
        return Manager::connection($connection);
    }
}

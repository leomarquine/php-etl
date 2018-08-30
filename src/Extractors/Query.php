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
     * The database manager.
     *
     * @var \Marquine\Etl\Database\Manager
     */
    protected $db;

    /**
     * Create a new Query Extractor instance.
     *
     * @param  \Marquine\Etl\Database\Manager  $manager
     * @return void
     */
    public function __construct(Manager $manager)
    {
        $this->db = $manager;
    }

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
        $statement = $this->db->pdo($this->connection)->prepare($this->query);

        $statement->execute($this->bindings);

        while ($row = $statement->fetch()) {
            yield $row;
        }
    }
}

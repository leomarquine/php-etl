<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Database\Manager;

class Query extends Extractor
{
    /**
     * The connection name.
     *
     * @var string
     */
    protected $connection = 'default';

    /**
     * Query bindings.
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * The prepared statement.
     *
     * @var \PDOStatement
     */
    protected $statement;

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
        'bindings', 'connection'
    ];

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
     * Set up the extraction from the given source.
     *
     * @param  mixed  $source
     * @return void
     */
    public function extract($source)
    {
        $this->statement = $this->db->pdo($this->connection)->prepare($source);
    }

    /**
     * Get the extractor iterator.
     *
     * @return \Generator
     */
    public function getIterator()
    {
        $this->statement->execute($this->bindings);

        while ($row = $this->statement->fetch()) {
            yield $row;
        }
    }
}

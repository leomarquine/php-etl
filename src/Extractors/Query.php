<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Row;
use Marquine\Etl\Database\Manager;

class Query extends Extractor
{
    /**
     * Query bindings.
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * The connection name.
     *
     * @var string
     */
    protected $connection = 'default';

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
     * Extract data from the input.
     *
     * @return \Generator
     */
    public function extract()
    {
        $statement = $this->db->pdo($this->connection)->prepare($this->input);

        $statement->execute($this->bindings);

        while ($row = $statement->fetch()) {
            yield new Row($row);
        }
    }
}

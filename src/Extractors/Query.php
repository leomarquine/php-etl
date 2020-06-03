<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Extractors;

use Wizaplace\Etl\Database\Manager;
use Wizaplace\Etl\Row;

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
     * @var \Wizaplace\Etl\Database\Manager
     */
    protected $db;

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'bindings', 'connection',
    ];

    /**
     * Create a new Query Extractor instance.
     *
     * @return void
     */
    public function __construct(Manager $manager)
    {
        $this->db = $manager;
    }

    /**
     * Extract data from the input.
     */
    public function extract(): \Generator
    {
        $statement = $this->db->pdo($this->connection)->prepare($this->input);

        $statement->execute($this->bindings);

        while ($row = $statement->fetch()) {
            yield new Row($row);
        }
    }
}

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
    public const BINDINGS = 'bindings';
    public const CONNECTION = 'connection';

    /**
     * Query bindings.
     */
    protected array $bindings = [];

    /**
     * The connection name.
     */
    protected string $connection = 'default';

    /**
     * The database manager.
     */
    protected Manager $db;

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected array $availableOptions = [
        self::BINDINGS,
        self::CONNECTION,
    ];

    /**
     * Create a new Query Extractor instance.
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

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

class Table extends Extractor
{
    public const CONNECTION = 'connection';
    public const WHERE = 'where';

    protected array $columns = ['*'];

    /**
     * The connection name.
     */
    protected string $connection = 'default';

    /**
     * The array of where clause.
     */
    protected array $where = [];

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
        self::COLUMNS,
        self::CONNECTION,
        self::WHERE,
    ];

    /**
     * Create a new Table Extractor instance.
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
        $statement = $this->db
            ->query($this->connection)
            ->select($this->input, $this->columns)
            ->where($this->where)
            ->execute();

        while ($row = $statement->fetch()) {
            yield new Row($row);
        }
    }
}

<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Wizaplace\Etl\Extractors;

use Wizaplace\Etl\Database\Manager;
use Wizaplace\Etl\Row;

class Table extends Extractor
{
    /**
     * Extractor columns.
     *
     * @var array
     */
    protected $columns = ['*'];

    /**
     * The connection name.
     *
     * @var string
     */
    protected $connection = 'default';

    /**
     * The array of where clause.
     *
     * @var array
     */
    protected $where = [];

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
        'columns', 'connection', 'where',
    ];

    /**
     * Create a new Table Extractor instance.
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

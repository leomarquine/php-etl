<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Row;
use Marquine\Etl\Database\Manager;

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
     * The array of where clause.
     *
     * @var array
     */
    protected $whereOp = [];

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
        'columns', 'connection', 'where'
    ];

    /**
     * Create a new Table Extractor instance.
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

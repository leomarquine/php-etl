<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Database\Manager;

class Table extends Extractor
{
    /**
     * The connection name.
     *
     * @var string
     */
    protected $connection = 'default';

    /**
     * Extractor columns.
     *
     * @var array
     */
    protected $columns;

    /**
     * The array of where clause.
     *
     * @var array
     */
    protected $where = [];

    /**
     * The table name.
     *
     * @var string
     */
    protected $table;

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
     * Set up the extraction from the given source.
     *
     * @param  mixed  $source
     * @return void
     */
    public function extract($source)
    {
        if (empty($this->columns)) {
            $this->columns = ['*'];
        }

        $this->table = $source;
    }

    /**
     * Get the extractor iterator.
     *
     * @return \Generator
     */
    public function getIterator()
    {
        $statement = $this->db
            ->query($this->connection)
            ->select($this->table, $this->columns)
            ->where($this->where)
            ->execute();

        while ($row = $statement->fetch()) {
            yield $row;
        }
    }
}

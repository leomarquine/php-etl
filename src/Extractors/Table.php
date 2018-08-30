<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Database\Manager;

class Table implements ExtractorInterface
{
    /**
     * The connection name.
     *
     * @var string
     */
    public $connection = 'default';

    /**
     * Extractor columns.
     *
     * @var array
     */
    public $columns;

    /**
     * The array of where clause.
     *
     * @var array
     */
    public $where = [];

    /**
     * The database table.
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
     * Create a new Table Extractor instance.
     *
     * @param \Marquine\Etl\Database\Manager $manager
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
        $this->table = $source;
    }

    /**
     * Get the extractor iterator.
     *
     * @return \Generator
     */
    public function getIterator()
    {
        if (empty($this->columns)) {
            $this->columns = ['*'];
        }

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

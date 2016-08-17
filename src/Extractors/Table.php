<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Etl;
use Marquine\Etl\Traits\Database;

class Table implements ExtractorInterface
{
    use Database;

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
     * Extract data from the given source.
     *
     * @param string $table
     * @return array
     */
    public function extract($table)
    {
        $this->connect($this->connection);

        if (is_string($this->columns)) {
            $this->columns = [$this->columns];
        }

        return $this->db->select($table, $this->columns, $this->where);
    }
}

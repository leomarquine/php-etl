<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Etl;

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
     * Extract data from the given source.
     *
     * @param string $table
     * @return array
     */
    public function extract($table)
    {
        if (is_string($this->columns)) {
            $this->columns = [$this->columns];
        }

        return Etl::database($this->connection)->select(
            $table, $this->columns, $this->where
        );
    }
}

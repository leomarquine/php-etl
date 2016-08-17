<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Etl;
use Marquine\Etl\Traits\Database;

class Query implements ExtractorInterface
{
    use Database;
    /**
     * The connection name.
     *
     * @var string
     */
    public $connection = 'default';

    /**
     * Query bindings.
     *
     * @var array
     */
    public $bindings = [];

    /**
     * Extract data from the given source.
     *
     * @param string $query
     * @return array
     */
    public function extract($query)
    {
        $this->connect($this->connection);

        $query = $this->db->prepare($query);

        $query->execute($this->bindings);

        return $query->fetchAll();
    }
}

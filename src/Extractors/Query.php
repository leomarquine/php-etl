<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Etl;

class Query implements ExtractorInterface
{
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
        $query = Etl::connection($this->connection)->prepare($query);

        $query->execute($this->bindings);

        return $query->fetchAll();
    }
}

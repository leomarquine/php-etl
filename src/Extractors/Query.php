<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Database\Manager as DB;

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
     * @param  string  $query
     * @return \Generator
     */
    public function extract($query)
    {
        $query = DB::connection($this->connection)->prepare($query);

        $query->execute($this->bindings);

        while ($row = $query->fetch()) {
            yield $row;
        }
    }
}

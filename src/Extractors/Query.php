<?php

namespace Marquine\Metis\Extractors;

use Marquine\Metis\Metis;
use Marquine\Metis\Traits\SetOptions;
use Marquine\Metis\Contracts\Extractor;

class Query implements Extractor
{
    use SetOptions;

    /**
     * The connection name.
     *
     * @var string
     */
    protected $connection = 'default';

    /**
     * Extract data from the given source.
     *
     * @param  string $query
     * @param  mixed  $bindings
     * @return array
     */
    public function extract($query, $bindings = null)
    {
        if (! $bindings) {
            $bindings = [];
        }

        $query = Metis::connection($this->connection)->prepare($query);

        $query->execute($bindings);

        return $query->fetchAll();
    }
}

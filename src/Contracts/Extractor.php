<?php

namespace Marquine\Metis\Contracts;

interface Extractor
{
    /**
     * Extract data from the given source.
     *
     * @param  mixed $source
     * @param  mixed $columns
     * @return array
     */
    public function extract($source, $columns);
}

<?php

namespace Marquine\Etl\Extractors;

interface ExtractorInterface
{
    /**
     * Extract data from the given source.
     *
     * @param  string  $source
     * @return \Generator
     */
    public function extract($source);
}

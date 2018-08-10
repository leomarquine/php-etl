<?php

namespace Marquine\Etl\Extractors;

interface ExtractorInterface
{
    /**
     * Set the extractor source.
     *
     * @param  mixed  $source
     * @return void
     */
    public function source($source);
};

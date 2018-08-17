<?php

namespace Marquine\Etl\Extractors;

use IteratorAggregate;

interface ExtractorInterface extends IteratorAggregate
{
    /**
     * Set the extractor source.
     *
     * @param  mixed  $source
     * @return void
     */
    public function source($source);
};

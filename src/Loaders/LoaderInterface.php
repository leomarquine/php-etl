<?php

namespace Marquine\Etl\Loaders;

use Generator;

interface LoaderInterface
{
    /**
     * Load data into the given destination.
     *
     * @param  \Generator  $data
     * @param  string  $destination
     * @return void
     */
    public function load(Generator $data, $destination);
}

<?php

namespace Marquine\Etl\Loaders;

use Generator;

abstract class Loader
{
    /**
     * Load data into the given destination.
     *
     * @param  \Generator  $data
     * @param  string  $destination
     * @return void
     */
    abstract public function load(Generator $data, $destination);
}

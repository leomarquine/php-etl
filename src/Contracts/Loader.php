<?php

namespace Marquine\Metis\Contracts;

interface Loader
{
    /**
     * Load data to the given destination.
     *
     * @param  string $destination
     * @param  array  $items
     * @return void
     */
    public function load($destination, $items);
}

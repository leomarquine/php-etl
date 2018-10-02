<?php

namespace Marquine\Etl\Loaders;

use Marquine\Etl\Step;

abstract class Loader extends Step
{
    /**
     * Get the loader handler.
     *
     * @param  mixed  $destination
     * @return callable
     */
    abstract public function load($destination);
}

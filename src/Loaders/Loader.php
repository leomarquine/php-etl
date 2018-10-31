<?php

namespace Marquine\Etl\Loaders;

use Marquine\Etl\Step;

abstract class Loader extends Step
{
    /**
     * Load the given row.
     *
     * @param  \Marquine\Etl\Row  $row
     * @return void
     */
    abstract public function load($row);
}

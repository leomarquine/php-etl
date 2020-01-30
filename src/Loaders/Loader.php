<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Wizaplace\Etl\Loaders;

use Wizaplace\Etl\Row;
use Wizaplace\Etl\Step;

abstract class Loader extends Step
{
    /**
     * The loader output.
     *
     * @var mixed
     */
    protected $output;

    /**
     * Set the loader output.
     *
     * @param mixed $output
     *
     * @return $this
     */
    public function output($output): Loader
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Load the given row.
     */
    abstract public function load(Row $row): void;
}

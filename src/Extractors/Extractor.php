<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Wizaplace\Etl\Extractors;

use Wizaplace\Etl\Step;

abstract class Extractor extends Step
{
    /**
     * The extractor input.
     *
     * @var mixed
     */
    protected $input;

    /**
     * Set the extractor input.
     *
     * @param mixed $input
     *
     * @return $this
     */
    public function input($input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Extract data from the input.
     */
    abstract public function extract(): \Generator;
}

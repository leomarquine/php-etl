<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Extractors;

use Wizaplace\Etl\Step;

abstract class Extractor extends Step
{
    /** @var mixed */
    protected $input;

    /**
     * Set the extractor input.
     *
     * @param mixed $input
     *
     * @return $this
     */
    public function input($input): Extractor
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Extract data from the input.
     */
    abstract public function extract(): \Generator;
}

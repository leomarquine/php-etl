<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Wizaplace\Etl;

abstract class Step
{
    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected $availableOptions = [];

    /**
     * Set the step options.
     *
     * @param string[]|int[] $options
     *
     * @return $this
     */
    public function options(array $options): Step
    {
        foreach ($options as $option => $value) {
            $option = lcfirst(implode('', array_map('ucfirst', explode('_', $option))));

            if (in_array($option, $this->availableOptions, true)) {
                $this->$option = $value;
            }
        }

        return $this;
    }

    /**
     * Initialize the step.
     */
    public function initialize(): void
    {
    }

    /**
     * Finalize the step.
     */
    public function finalize(): void
    {
    }
}

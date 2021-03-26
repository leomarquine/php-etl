<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl;

abstract class Step
{
    public const COLUMNS = 'columns';
    public const INDEX = 'index';

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected array $availableOptions = [];

    /**
     * Set the step options.
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

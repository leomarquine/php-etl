<?php

namespace Marquine\Etl;

abstract class Step
{
    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [];

    /**
     * Set the step options.
     *
     * @param  array  $options
     * @return $this
     */
    public function options(array $options)
    {
        foreach ($options as $option => $value) {
            $option = lcfirst(implode('', array_map('ucfirst', explode('_', $option))));

            if (in_array($option, $this->availableOptions)) {
                $this->$option = $value;
            }
        }

        return $this;
    }

    /**
     * Initialize the step.
     *
     * @return void
     */
    public function initialize()
    {
        //
    }

    /**
     * Finalize the step.
     *
     * @return void
     */
    public function finalize()
    {
        //
    }
}

<?php

namespace Marquine\Etl;

abstract class Step
{
    /**
     * The step pipeline.
     *
     * @var \Marquine\Etl\Pipeline
     */
    protected $pipeline;

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [];

    /**
     * Set the step pipeline.
     *
     * @param  \Marquine\Etl\Pipeline  $pipeline
     * @return $this
     */
    public function pipeline(Pipeline $pipeline)
    {
        $this->pipeline = $pipeline;

        return $this;
    }

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
}

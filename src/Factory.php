<?php

namespace Marquine\Etl;

use ReflectionClass;
use InvalidArgumentException;

class Factory
{
    /**
     * Step instance.
     *
     * @var mixed
     */
    protected $instance;

    /**
     * Make a new step and set its options.
     *
     * @param  string  $type
     * @param  string  $step
     * @param  array|null  $options
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function make($type, $step, $options = null)
    {
        $this->createStep($type, $step);

        if ($this->instance instanceof $type) {
            $this->setStepOptions($options);

            return $this->instance;
        }

        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];

        throw new InvalidArgumentException("$step is not a valid '$caller' step.");
    }

    /**
     * Create a new instance of the step.
     *
     * @param  string  $type
     * @param  string  $step
     * @return void
     */
    protected function createStep($type, $step)
    {
        if (class_exists($step)) {
            $this->instance = new $step;
        } elseif (class_exists($step = $this->guessStepClass($type, $step))) {
            $this->instance = new $step;
        }
    }

    /**
     * Guess the step class name.
     *
     * @param  string  $type
     * @param  string  $step
     * @return string
     */
    protected function guessStepClass($type, $step)
    {
        $namespace = (new ReflectionClass($type))->getNamespaceName();

        $class = implode('', array_map('ucfirst', explode(' ', str_replace(['-', '_'], ' ', $step))));

        return $namespace.'\\'.$class;
    }

    /**
     * Set the options of the step.
     *
     * @return void
     */
    protected function setStepOptions($options)
    {
        if (empty($options)) {
            return;
        }

        $reflector = new ReflectionClass($this->instance);

        foreach ($options as $property => $value) {
            if ($reflector->hasProperty($property) && $reflector->getProperty($property)->isPublic()) {
                $this->instance->$property = $value;
            }
        }
    }
}

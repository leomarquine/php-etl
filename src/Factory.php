<?php

namespace Marquine\Etl;

use ReflectionClass;
use InvalidArgumentException;

class Factory
{
    /**
     * Make a new step.
     *
     * @param  mixed  $class
     * @param  string  $interface
     * @param  array  $options
     * @return object
     */
    public function make($class, $interface, $options)
    {
        if (is_string($class)) {
            $class = $this->instance($class, $interface);
        }

        $this->setOptions($class, $options);

        return $this->validate($class, $interface);
    }

    /**
     * Create a step instance.
     *
     * @param  string  $class
     * @param  string  $interface
     * @return object
     *
     * @throws \InvalidArgumentException
     */
    protected function instance($class, $interface)
    {
        $class = $this->guessStepClass($class, $interface);

        if (class_exists($class)) {
            return $this->build($class);
        }

        $step = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'];

        throw new InvalidArgumentException("$class is not a valid '$step' step.");
    }

    /**
     * Validate the step against the given interface.
     *
     * @param  mixed  $class
     * @param  string  $interface
     * @return object
     *
     * @throws \InvalidArgumentException
     */
    protected function validate($class, $interface)
    {
        if ($class instanceof $interface) {
            return $class;
        }

        $step = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'];

        $class = get_class($class);

        throw new InvalidArgumentException("The class '$class' is not a valid '$step' step.");
    }

    /**
     * Recursively build a step and its dependencies.
     *
     * @param  string  $class
     * @return object
     */
    protected function build($class)
    {
        $reflector = new ReflectionClass($class);

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $class;
        }

        $dependencies = $constructor->getParameters();

        $arguments = [];

        foreach ($dependencies as $dependency) {
            if ($dependency->getClass()) {
                $arguments[] = $this->build($dependency->getCLass()->name);
            }
        }

        return $reflector->newInstanceArgs($arguments);
    }

    /**
     * Guess the step class.
     *
     * @param  string  $step
     * @param  string  $interface
     * @return string
     */
    protected function guessStepClass($step, $interface)
    {
        if (class_exists($step)) {
            return $step;
        }

        $namespace = (new ReflectionClass($interface))->getNamespaceName();

        $class = implode('', array_map('ucfirst', explode(' ', str_replace(['-', '_'], ' ', $step))));

        return $namespace . '\\' . $class;
    }

    /**
     * Set the step options.
     *
     * @param  object  $class
     * @param  array  $options
     * @return void
     */
    protected function setOptions($class, $options)
    {
        if (empty($options)) {
            return;
        }

        $reflector = new ReflectionClass($class);

        foreach ($options as $property => $value) {
            $property = lcfirst(implode('', array_map('ucfirst', explode(' ', str_replace(['-', '_'], ' ', $property)))));

            if ($reflector->hasProperty($property) && $reflector->getProperty($property)->isPublic()) {
                $class->$property = $value;
            }
        }
    }
}

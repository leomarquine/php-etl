<?php

namespace Marquine\Etl;

use InvalidArgumentException;
use Illuminate\Container\Container as BaseContainer;

class Container extends BaseContainer
{
    /**
     * The current globally available container (if any).
     *
     * @var static
     */
    protected static $instance;

    /**
     * Set the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;

            require __DIR__.'/bindings.php';
        }

        return static::$instance;
    }

    /**
     * Make an etl step.
     *
     * @param  mixed  $step
     * @param  string  $abstract
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function step($step, $abstract)
    {
        $name = is_string($step) ? $step : get_class($step);
        $type = strtolower(substr(strrchr($abstract, '\\'), 1));

        if (is_string($step)) {
            if (class_exists($step)) {
                $step = $this->make($step);
            }

            if ($this->has("{$name}_{$type}")) {
                $step = $this->make("{$name}_{$type}");
            }
        }

        if ($step instanceof $abstract) {
            return $step;
        }

        throw new InvalidArgumentException("The step [$name] is not a valid $type.");
    }
}

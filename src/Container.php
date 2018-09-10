<?php

namespace Marquine\Etl;

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
}

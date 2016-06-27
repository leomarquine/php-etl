<?php

namespace Marquine\Metis\Traits;

trait SetOptions
{
    /**
     * Create a new class instance and set its properties.
     *
     * @param  array $options
     * @return void
     */
    public function __construct($options)
    {
        foreach ($options as $option => $value) {
            if (property_exists($this, $option)) {
                $this->{$option} = $value;
            }
        }
    }
}

<?php

namespace Marquine\Etl;

use ArrayAccess;

class Row implements ArrayAccess
{
    /**
     * Row attributes.
     *
     * @var array
     */
    protected $attributes;

    /**
     * Determine if the row will be discarded.
     *
     * @var bool
     */
    protected $discarded = false;

    /**
     * Create a new Row instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Set a row attribute
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get a row attribute.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Remove a row attribute.
     *
     * @param  string  $key
     * @return void
     */
    public function remove($key)
    {
        unset($this->attributes[$key]);
    }

    /**
     * Transform the given columns using a callback.
     *
     * @param  array  $columns
     * @param  callable  $callback
     * @return void
     */
    public function transform(array $columns, callable $callback)
    {
        if (empty($columns)) {
            $columns = array_keys($this->attributes);
        }

        foreach ($columns as $column) {
            $this->$column = call_user_func($callback, $this->$column);
        }
    }

    /**
     * Get the array representation of the row.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Discard the row.
     *
     * @return void
     */
    public function discard()
    {
        $this->discarded = true;
    }

    /**
     * Check if the row was discarded.
     *
     * @return bool
     */
    public function discarded()
    {
        return $this->discarded;
    }

    /**
     * Dynamically retrieve attributes on the row.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes[$key];
    }

    /**
     * Dynamically set attributes on the row.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->attributes[$offset];
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }
}

<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl;

class Row implements \ArrayAccess
{
    /**
     * Row attributes.
     */
    protected array $attributes;

    /**
     * Determine if the row will be discarded.
     */
    protected bool $discarded = false;

    /**
     * Flag the row as incomplete.
     */
    protected bool $incomplete = false;

    /**
     * Create a new Row instance.
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Set a row attribute.
     *
     * @param mixed $value
     */
    public function set(string $key, $value): self
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Get a row attribute.
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Remove a row attribute.
     */
    public function remove(string $key): void
    {
        unset($this->attributes[$key]);
    }

    /**
     * Get a row attribute, and remove it.
     *
     * @return mixed
     */
    public function pull(string $key)
    {
        $value = $this->attributes[$key] ?? null;

        unset($this->attributes[$key]);

        return $value;
    }

    /**
     * Transform the given columns using a callback.
     *
     * @param string[] $columns
     */
    public function transform(array $columns, callable $callback): void
    {
        if ([] === $columns) {
            $columns = array_keys($this->attributes);
        }

        foreach ($columns as $column) {
            $this->$column = $callback($this->$column);
        }
    }

    /**
     * Get the array representation of the row.
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Discard the row.
     */
    public function discard(): self
    {
        $this->discarded = true;

        return $this;
    }

    /**
     * Check if the row was discarded.
     */
    public function discarded(): bool
    {
        return $this->discarded;
    }

    /**
     * Set the row dirty.
     */
    public function setIncomplete(): self
    {
        $this->incomplete = true;

        return $this;
    }

    /**
     * Check if the row is dirty.
     */
    public function isIncomplete(): bool
    {
        return $this->incomplete;
    }

    /**
     * Dynamically retrieve attributes on the row.
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->attributes[$key];
    }

    /**
     * Dynamically set attributes on the row.
     *
     * @param mixed $value
     */
    public function __set(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Get the value for a given offset.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->attributes[$offset];
    }

    /**
     * Set the value for a given offset.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Override all the attributes of the row or merge
     * them with the given ones.
     *
     * @param bool $merge
     */
    public function setAttributes(array $newAttributes, $merge = false): void
    {
        if ($merge) {
            $this->attributes = array_merge($this->attributes, $newAttributes);
        } else {
            $this->attributes = $newAttributes;
        }
    }

    /**
     * Clear all the attributes of the row.
     */
    public function clearAttributes(): void
    {
        $this->attributes = [];
    }
}

<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Loaders;

use Wizaplace\Etl\Row;

class MemoryLoader extends Loader
{
    /**
     * Which Row attribute (column) to use as collection index.
     */
    protected string $index;

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected array $availableOptions = ['index'];

    /** @var array<string, mixed> In memory loaded collection */
    protected array $collection;

    public function load(Row $row): void
    {
        $this->collection[$row->get($this->index)] = $row;
    }

    public function get(string $index): ?Row
    {
        return $this->collection[$index] ?? null;
    }
}

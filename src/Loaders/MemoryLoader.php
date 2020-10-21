<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Loaders;

use Wizaplace\Etl\Loaders\Loader;
use Wizaplace\Etl\Row;

class MemoryLoader extends Loader
{
    /** @var string $index Which Row attribute (column) to use as collection index. */
    protected $index;

    /**
     * Properties that can be set via the options method. Supports:
     *   - index: which Row attribute (column) to use as collection index.
     *
     * @var string[]
     */
    protected $availableOptions = [
        'index',
    ];

    /** @var array<string, mixed> In memory loaded collection */
    protected $collection;

    /**
     * @inheritDoc
     */
    public function load(Row $row): void
    {
        $this->collection[$row->get($this->index)] = $row;
    }

    public function get(string $index): ?Row
    {
        return $this->collection[$index] ?? null;
    }
}

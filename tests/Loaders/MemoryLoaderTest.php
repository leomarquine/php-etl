<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Loaders;

use PHPUnit\Framework\TestCase;
use Wizaplace\Etl\Loaders\MemoryLoader;
use Wizaplace\Etl\Row;

class MemoryLoaderTest extends TestCase
{
    protected MemoryLoader $loader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loader = new MemoryLoader();
    }

    public function testLoadMultipleRowsWithValidIndex(): void
    {
        $row1 = new Row(['a' => 'b', 'c' => $index1 = 'd']);
        $row2 = new Row(['a' => 'b2', 'c' => $index2 = 'd2']);

        $this->loader->options(['index' => 'c']);
        $this->loader->load($row1);
        $this->loader->load($row2);

        static::assertSame($row2, $this->loader->get($index2));
        static::assertSame($row1, $this->loader->get($index1));
    }

    public function testLoadRowWithoutValidIndex(): void
    {
        $row = new Row(['a' => 'b', 'c' => $index = 'd']);

        $this->loader->options(['index' => 'k']);
        $this->loader->load($row);

        static::assertNull($this->loader->get($index));
    }

    public function testLoadRowGetNotFound(): void
    {
        $row = new Row(['a' => 'b', 'c' => $index = 'd']);

        $this->loader->options(['index' => 'c']);
        $this->loader->load($row);

        static::assertNull($this->loader->get('not found'));
    }
}

<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @author      Karl DeBisschop <kdebisschop@gmail.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Transformers;

use Tests\TestCase;
use Wizaplace\Etl\Row;
use Wizaplace\Etl\Transformers\CopyColumns;

class CopyColumnsTest extends TestCase
{
    /** @test */
    public function copyColumn(): void
    {
        $data = [
            new Row(['id' => '1', 'name' => 'John Doe']),
            new Row(['id' => '2', 'name' => 'Jane Doe']),
        ];

        $expected = [
            new Row(['id' => '1', 'name' => 'John Doe', 'surname' => 'John Doe']),
            new Row(['id' => '2', 'name' => 'Jane Doe', 'surname' => 'Jane Doe']),
        ];

        $transformer = new CopyColumns();

        $transformer->options([$transformer::COLUMNS => ['name' => 'surname']]);

        $this->execute($transformer, $data);

        static::assertEquals($expected, $data);
    }
}

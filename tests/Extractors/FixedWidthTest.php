<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Extractors;

use Tests\TestCase;
use Wizaplace\Etl\Extractors\FixedWidth;
use Wizaplace\Etl\Row;

class FixedWidthTest extends TestCase
{
    /** @test */
    public function columnsStartAndLength(): void
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new FixedWidth();

        $extractor->input(__DIR__ . '/../data/fixed-width.txt');
        $extractor->options(['columns' => ['id' => [0, 1], 'name' => [1, 8], 'email' => [9, 17]]]);

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }
}

<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\FixedWidth;

class FixedWidthTest extends TestCase
{
    /** @test */
    public function columns_start_and_length()
    {
        $expected = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $extractor = new FixedWidth;

        $extractor->options(['columns' => ['id' => [0, 1], 'name' => [1, 8], 'email' => [9, 17]]]);

        $iterator = $extractor->extract(__DIR__.'/../data/fixed-width.txt');

        $this->assertEquals($expected, iterator_to_array($iterator));
    }
}

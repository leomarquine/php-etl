<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Row;
use Marquine\Etl\Extractors\FixedWidth;

class FixedWidthTest extends TestCase
{
    /** @test */
    public function columns_start_and_length()
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new FixedWidth;

        $extractor->input(__DIR__.'/../data/fixed-width.txt');
        $extractor->options(['columns' => ['id' => [0, 1], 'name' => [1, 8], 'email' => [9, 17]]]);

        $this->assertEquals($expected, iterator_to_array($extractor->extract()));
    }
}

<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\FixedWidth;

class FixedWidthTest extends TestCase
{
    protected $expected = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    public function extracts_data_from_a_fixed_width_text_file()
    {
        $extractor = new FixedWidth;

        $extractor->columns = [
            'id' => [0, 1],
            'name' => [1, 8],
            'email' => [9, 17],
        ];

        $extractor->source(__DIR__.'/../data/fixed-width.txt');

        $this->assertEquals($this->expected, iterator_to_array($extractor));
    }
}

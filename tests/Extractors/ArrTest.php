<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\Arr;

class ArrTest extends TestCase
{
    protected $items = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    public function extracts_data_from_an_array_with_default_options()
    {
        $extractor = new Arr;

        $extractor->source($this->items);

        $this->assertEquals($this->items, iterator_to_array($extractor));
    }

    /** @test */
    public function extracts_data_from_an_array_with_custom_options()
    {
        $expected = [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Doe'],
        ];

        $extractor = new Arr;

        $extractor->columns = ['id', 'name'];

        $extractor->source($this->items);

        $this->assertEquals($expected, iterator_to_array($extractor));
    }
}

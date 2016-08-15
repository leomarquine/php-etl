<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\ArrayData;

class ArrayDataTest extends TestCase
{
    private $items = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    function extracts_data_from_an_array()
    {
        $extractor = new ArrayData;

        $results = $extractor->extract($this->items);

        $this->assertEquals($this->items, $results);
    }

    /** @test */
    function extracts_specific_columns_from_an_array()
    {
        $expected = [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Doe'],
        ];

        $extractor = new ArrayData;

        $extractor->columns = ['id', 'name'];

        $results = $extractor->extract($this->items);

        $this->assertEquals($expected, $results);
    }
}

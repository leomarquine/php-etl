<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\Arr;

class ArrTest extends TestCase
{
    private $items = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    function extracts_data_from_an_array()
    {
        $extractor = new Arr;

        $results = $extractor->extract($this->items);

        $this->assertEquals($this->items, iterator_to_array($results));
    }

    /** @test */
    function extracts_specific_columns_from_an_array()
    {
        $expected = [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Doe'],
        ];

        $extractor = new Arr;

        $extractor->columns = ['id', 'name'];

        $results = $extractor->extract($this->items);

        $this->assertEquals($expected, iterator_to_array($results));
    }
}

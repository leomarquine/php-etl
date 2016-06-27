<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Metis\Metis;

class ArrayDataTest extends TestCase
{
    private $source = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    function extracts_data_from_an_array()
    {
        $results = Metis::extract('array', $this->source)->get();

        $this->assertEquals($this->source, $results);
    }

    /** @test */
    function extracts_specific_columns_from_an_array()
    {
        $expected = [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Doe'],
        ];

        $columns = ['id', 'name'];

        $results = Metis::extract('array', $this->source, $columns)->get();

        $this->assertEquals($expected, $results);
    }
}

<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Metis\Extractors\FixedWidth;

class FixedWidthTest extends TestCase
{
    private $expected = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    function extracts_data_from_a_fixed_width_text_file()
    {
        $columns = [
            'id' => [0, 1],
            'name' => [1, 8],
            'email' => [9, 17],
        ];

        $extractor = new FixedWidth;

        $results = $extractor->extract('users.txt', $columns);

        $this->assertEquals($this->expected, $results);
    }
}

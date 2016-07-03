<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Metis\Extractors\Json;

class JsonTest extends TestCase
{
    private $expected = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    function extracts_data_from_a_json_file()
    {
        $extractor = new Json;

        $results = $extractor->extract('users.json');

        $this->assertEquals($this->expected, $results);
    }

    /** @test */
    function extracts_data_from_a_json_file_with_custom_attributes_path()
    {
        $columns =  [
            'id' => '$..bindings[*].id.value',
            'name' => '$..bindings[*].name.value',
            'email' => '$..bindings[*].email.value'
        ];

        $extractor = new Json;

        $results = $extractor->extract('users_path.json', $columns);

        $this->assertEquals($this->expected, $results);
    }
}

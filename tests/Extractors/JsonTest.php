<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\Json;

class JsonTest extends TestCase
{
    protected $expected = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    public function extracts_data_from_a_json_file()
    {
        $extractor = new Json;

        $extractor->source(__DIR__.'/../data/json1.json');

        $this->assertEquals($this->expected, iterator_to_array($extractor));
    }

    /** @test */
    public function extracts_data_from_a_json_file_with_custom_attributes_path()
    {
        $extractor = new Json;

        $extractor->columns = [
            'id' => '$..bindings[*].id.value',
            'name' => '$..bindings[*].name.value',
            'email' => '$..bindings[*].email.value'
        ];

        $extractor->source(__DIR__.'/../data/json2.json');

        $this->assertEquals($this->expected, iterator_to_array($extractor));
    }
}

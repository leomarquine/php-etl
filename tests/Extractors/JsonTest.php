<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\Json;

class JsonTest extends TestCase
{
    /** @test */
    public function default_options()
    {
        $expected = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $extractor = new Json;

        $extractor->extract(__DIR__.'/../data/json1.json');

        $this->assertEquals($expected, iterator_to_array($extractor));
    }

    /** @test */
    public function custom_columns_json_path()
    {
        $expected = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $extractor = new Json;

        $extractor->options(['columns' => [
            'id' => '$..bindings[*].id.value',
            'name' => '$..bindings[*].name.value',
            'email' => '$..bindings[*].email.value',
        ]]);

        $extractor->extract(__DIR__.'/../data/json2.json');

        $this->assertEquals($expected, iterator_to_array($extractor));
    }
}

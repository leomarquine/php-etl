<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Etl\Transformers\JsonEncode;

class JsonEncodeTest extends TestCase
{
    /** @test */
    public function convert_all_columns_to_json()
    {
        $items = [
            ['id' => '1', 'data' => ['name' => 'John Doe', 'email' => 'johndoe@email.com']],
            ['id' => '2', 'data' => ['name' => 'Jane Doe', 'email' => 'janedoe@email.com']],
        ];

        $transformer = new JsonEncode;

        $results = array_map($transformer->handler(), $items);

        $expected = [
            ['id' => '"1"', 'data' => '{"name":"John Doe","email":"johndoe@email.com"}'],
            ['id' => '"2"', 'data' => '{"name":"Jane Doe","email":"janedoe@email.com"}'],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    public function convert_specific_columns_to_json()
    {
        $items = [
            ['id' => '1', 'data' => ['name' => 'John Doe', 'email' => 'johndoe@email.com']],
            ['id' => '2', 'data' => ['name' => 'Jane Doe', 'email' => 'janedoe@email.com']],
        ];

        $transformer = new JsonEncode;

        $transformer->columns = ['data'];

        $results = array_map($transformer->handler(), $items);

        $expected = [
            ['id' => '1', 'data' => '{"name":"John Doe","email":"johndoe@email.com"}'],
            ['id' => '2', 'data' => '{"name":"Jane Doe","email":"janedoe@email.com"}'],
        ];

        $this->assertEquals($expected, $results);
    }
}

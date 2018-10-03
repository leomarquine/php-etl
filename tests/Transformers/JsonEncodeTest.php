<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Etl\Transformers\JsonEncode;

class JsonEncodeTest extends TestCase
{
    protected $data = [
        ['id' => '1', 'data' => ['name' => 'John Doe', 'email' => 'johndoe@email.com']],
        ['id' => '2', 'data' => ['name' => 'Jane Doe', 'email' => 'janedoe@email.com']],
    ];

    /** @test */
    public function default_options()
    {
        $expected = [
            ['id' => '"1"', 'data' => '{"name":"John Doe","email":"johndoe@email.com"}'],
            ['id' => '"2"', 'data' => '{"name":"Jane Doe","email":"janedoe@email.com"}'],
        ];

        $transformer = new JsonEncode;

        $this->assertEquals($expected, array_map($transformer->transform(), $this->data));
    }

    /** @test */
    public function custom_columns()
    {
        $expected = [
            ['id' => '1', 'data' => '{"name":"John Doe","email":"johndoe@email.com"}'],
            ['id' => '2', 'data' => '{"name":"Jane Doe","email":"janedoe@email.com"}'],
        ];

        $transformer = new JsonEncode;

        $transformer->options(['columns' => ['data']]);

        $this->assertEquals($expected, array_map($transformer->transform(), $this->data));
    }
}

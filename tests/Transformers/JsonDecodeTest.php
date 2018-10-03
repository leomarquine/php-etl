<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Etl\Transformers\JsonDecode;

class JsonDecodeTest extends TestCase
{
    protected $data = [
        ['id' => '"1"', 'data' => '{"name":"John Doe","email":"johndoe@email.com"}'],
        ['id' => '"2"', 'data' => '{"name":"Jane Doe","email":"janedoe@email.com"}'],
    ];

    /** @test */
    public function default_options()
    {
        $expected = [
            ['id' => '1', 'data' => (object) ['name' => 'John Doe', 'email' => 'johndoe@email.com']],
            ['id' => '2', 'data' => (object) ['name' => 'Jane Doe', 'email' => 'janedoe@email.com']],
        ];

        $transformer = new JsonDecode;

        $this->assertEquals($expected, array_map($transformer->transform(), $this->data));
    }

    /** @test */
    public function converting_objects_to_associative_arrays()
    {
        $expected = [
            ['id' => '1', 'data' => ['name' => 'John Doe', 'email' => 'johndoe@email.com']],
            ['id' => '2', 'data' => ['name' => 'Jane Doe', 'email' => 'janedoe@email.com']],
        ];

        $transformer = new JsonDecode;

        $transformer->options(['assoc' => true]);

        $this->assertEquals($expected, array_map($transformer->transform(), $this->data));
    }

    /** @test */
    public function custom_columns()
    {
        $expected = [
            ['id' => '"1"', 'data' => (object) ['name' => 'John Doe', 'email' => 'johndoe@email.com']],
            ['id' => '"2"', 'data' => (object) ['name' => 'Jane Doe', 'email' => 'janedoe@email.com']],
        ];

        $transformer = new JsonDecode;

        $transformer->options(['columns' => ['data']]);

        $this->assertEquals($expected, array_map($transformer->transform(), $this->data));
    }
}

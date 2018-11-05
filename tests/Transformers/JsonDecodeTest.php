<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Etl\Row;
use Marquine\Etl\Transformers\JsonDecode;

class JsonDecodeTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->data = [
            new Row(['id' => '"1"', 'data' => '{"name":"John Doe","email":"johndoe@email.com"}']),
            new Row(['id' => '"2"', 'data' => '{"name":"Jane Doe","email":"janedoe@email.com"}']),
        ];
    }

    /** @test */
    public function default_options()
    {
        $expected = [
            new Row(['id' => '1', 'data' => (object) ['name' => 'John Doe', 'email' => 'johndoe@email.com']]),
            new Row(['id' => '2', 'data' => (object) ['name' => 'Jane Doe', 'email' => 'janedoe@email.com']]),
        ];

        $transformer = new JsonDecode;

        $this->execute($transformer, $this->data);

        $this->assertEquals($expected, $this->data);
    }

    /** @test */
    public function converting_objects_to_associative_arrays()
    {
        $expected = [
            new Row(['id' => '1', 'data' => ['name' => 'John Doe', 'email' => 'johndoe@email.com']]),
            new Row(['id' => '2', 'data' => ['name' => 'Jane Doe', 'email' => 'janedoe@email.com']]),
        ];

        $transformer = new JsonDecode;

        $transformer->options(['assoc' => true]);

        $this->execute($transformer, $this->data);

        $this->assertEquals($expected, $this->data);
    }

    /** @test */
    public function custom_columns()
    {
        $expected = [
            new Row(['id' => '"1"', 'data' => (object) ['name' => 'John Doe', 'email' => 'johndoe@email.com']]),
            new Row(['id' => '"2"', 'data' => (object) ['name' => 'Jane Doe', 'email' => 'janedoe@email.com']]),
        ];

        $transformer = new JsonDecode;

        $transformer->options(['columns' => ['data']]);

        $this->execute($transformer, $this->data);

        $this->assertEquals($expected, $this->data);
    }
}

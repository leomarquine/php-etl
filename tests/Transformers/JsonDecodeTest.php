<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Etl\Transformers\JsonDecode;

class JsonDecodeTest extends TestCase
{
    protected $items = [
        ['id' => '"1"', 'data' => '{"name":"John Doe","email":"johndoe@email.com"}'],
        ['id' => '"2"', 'data' => '{"name":"Jane Doe","email":"janedoe@email.com"}'],
    ];

    /** @test */
    public function decode_all_columns()
    {
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');

        $transformer = new JsonDecode;

        $transformer->assoc = true;

        $results = array_map($transformer->handler($pipeline), $this->items);

        $expected = [
            ['id' => '1', 'data' => ['name' => 'John Doe', 'email' => 'johndoe@email.com']],
            ['id' => '2', 'data' => ['name' => 'Jane Doe', 'email' => 'janedoe@email.com']],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    public function decode_specifc_columns()
    {
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');

        $transformer = new JsonDecode;

        $transformer->assoc = true;
        $transformer->columns = ['data'];

        $results = array_map($transformer->handler($pipeline), $this->items);

        $expected = [
            ['id' => '"1"', 'data' => ['name' => 'John Doe', 'email' => 'johndoe@email.com']],
            ['id' => '"2"', 'data' => ['name' => 'Jane Doe', 'email' => 'janedoe@email.com']],
        ];

        $this->assertEquals($expected, $results);
    }
}

<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Etl\Transformers\ConvertCase;

class ConvertCaseTest extends TestCase
{
    protected $items = [
        ['id' => '1', 'name' => 'jane doe', 'email' => 'janedoe@email.com'],
        ['id' => '2', 'name' => 'JOHN DOE', 'email' => 'JOHNDOE@EMAIL.COM'],
    ];

    /** @test */
    public function lowercase()
    {
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');

        $transformer = new ConvertCase;
        $transformer->mode = 'lower';

        $results = array_map($transformer->handler($pipeline), $this->items);

        $expected = [
            ['id' => '1', 'name' => 'jane doe', 'email' => 'janedoe@email.com'],
            ['id' => '2', 'name' => 'john doe', 'email' => 'johndoe@email.com'],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    public function uppercase()
    {
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');

        $transformer = new ConvertCase;
        $transformer->mode = 'upper';

        $results = array_map($transformer->handler($pipeline), $this->items);

        $expected = [
            ['id' => '1', 'name' => 'JANE DOE', 'email' => 'JANEDOE@EMAIL.COM'],
            ['id' => '2', 'name' => 'JOHN DOE', 'email' => 'JOHNDOE@EMAIL.COM'],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    public function titlecase()
    {
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');

        $transformer = new ConvertCase;
        $transformer->mode = 'title';
        $transformer->columns = ['name'];

        $results = array_map($transformer->handler($pipeline), $this->items);

        $expected = [
            ['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
            ['id' => '2', 'name' => 'John Doe', 'email' => 'JOHNDOE@EMAIL.COM'],
        ];

        $this->assertEquals($expected, $results);
    }
}

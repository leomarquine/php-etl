<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Etl\Transformers\Trim;

class TrimTest extends TestCase
{
    protected $items = [
        ['id' => ' 1', 'name' => 'John Doe  ', 'email' => ' johndoe@email.com '],
        ['id' => '2 ', 'name' => '  Jane Doe', 'email' => '  janedoe@email.com  '],
    ];

    /** @test */
    public function trim_all_columns()
    {
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');

        $transformer = new Trim;

        $results = array_map($transformer->handler($pipeline), $this->items);

        $expected = [
            ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    public function trim_specific_columns()
    {
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');

        $transformer = new Trim;
        $transformer->columns = ['id', 'name'];

        $results = array_map($transformer->handler($pipeline), $this->items);

        $expected = [
            ['id' => '1', 'name' => 'John Doe', 'email' => ' johndoe@email.com '],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => '  janedoe@email.com  '],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    public function trim_right()
    {
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');

        $transformer = new Trim;
        $transformer->type = 'right';

        $results = array_map($transformer->handler($pipeline), $this->items);

        $expected = [
            ['id' => ' 1', 'name' => 'John Doe', 'email' => ' johndoe@email.com'],
            ['id' => '2', 'name' => '  Jane Doe', 'email' => '  janedoe@email.com'],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    public function trim_left()
    {
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');

        $transformer = new Trim;
        $transformer->type = 'left';

        $results = array_map($transformer->handler($pipeline), $this->items);

        $expected = [
            ['id' => '1', 'name' => 'John Doe  ', 'email' => 'johndoe@email.com '],
            ['id' => '2 ', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com  '],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    public function trim_with_custom_character_mask()
    {
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');

        $transformer = new Trim;
        $transformer->mask = ' cmo.';

        $results = array_map($transformer->handler($pipeline), $this->items);

        $expected = [
            ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email'],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email'],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    public function throws_an_exception_for_unsupported_trim_type()
    {
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');

        $transformer = new Trim;
        $transformer->type = 'invalid';

        $this->expectException('InvalidArgumentException');

        $transformer->handler($pipeline);
    }
}

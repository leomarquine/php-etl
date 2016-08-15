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
    function trim_all_columns()
    {
        $transformer = new Trim;

        $results = $transformer->transform($this->items);

        $expected = [
            ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function trim_specific_columns()
    {
        $transformer = new Trim;

        $transformer->columns = ['id', 'name'];

        $results = $transformer->transform($this->items);

        $expected = [
            ['id' => '1', 'name' => 'John Doe', 'email' => ' johndoe@email.com '],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => '  janedoe@email.com  '],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function trim_right()
    {
        $transformer = new Trim;

        $transformer->type = 'right';

        $results = $transformer->transform($this->items);

        $expected = [
            ['id' => ' 1', 'name' => 'John Doe', 'email' => ' johndoe@email.com'],
            ['id' => '2', 'name' => '  Jane Doe', 'email' => '  janedoe@email.com'],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function trim_left()
    {
        $transformer = new Trim;

        $transformer->type = 'left';

        $results = $transformer->transform($this->items);

        $expected = [
            ['id' => '1', 'name' => 'John Doe  ', 'email' => 'johndoe@email.com '],
            ['id' => '2 ', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com  '],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function trim_with_custom_character_mask()
    {
        $transformer = new Trim;

        $transformer->mask = ' cmo.';

        $results = $transformer->transform($this->items);

        $expected = [
            ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email'],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email'],
        ];

        $this->assertEquals($expected, $results);
    }
}

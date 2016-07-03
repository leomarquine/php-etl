<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Metis\Transformers\Trim;

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
        $columns = ['id', 'name'];

        $transformer = new Trim;

        $results = $transformer->transform($this->items, $columns);

        $expected = [
            ['id' => '1', 'name' => 'John Doe', 'email' => ' johndoe@email.com '],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => '  janedoe@email.com  '],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function trim_right()
    {
        $options = ['type' => 'right'];

        $transformer = new Trim($options);

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
        $options = ['type' => 'left'];

        $transformer = new Trim($options);

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
        $options = ['mask' => ' cmo.'];

        $transformer = new Trim($options);

        $results = $transformer->transform($this->items);

        $expected = [
            ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email'],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email'],
        ];

        $this->assertEquals($expected, $results);
    }
}

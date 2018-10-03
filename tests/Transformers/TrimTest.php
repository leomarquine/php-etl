<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Etl\Transformers\Trim;

class TrimTest extends TestCase
{
    protected $data = [
        ['id' => ' 1', 'name' => 'John Doe  ', 'email' => ' johndoe@email.com '],
        ['id' => '2 ', 'name' => '  Jane Doe', 'email' => '  janedoe@email.com  '],
    ];

    /** @test */
    public function default_options()
    {
        $expected = [
            ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $transformer = new Trim;

        $this->assertEquals($expected, array_map($transformer->transform(), $this->data));
    }

    /** @test */
    public function custom_columns()
    {
        $expected = [
            ['id' => '1', 'name' => 'John Doe', 'email' => ' johndoe@email.com '],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => '  janedoe@email.com  '],
        ];

        $transformer = new Trim;

        $transformer->options(['columns' => ['id', 'name']]);

        $this->assertEquals($expected, array_map($transformer->transform(), $this->data));
    }

    /** @test */
    public function trim_right()
    {
        $expected = [
            ['id' => ' 1', 'name' => 'John Doe', 'email' => ' johndoe@email.com'],
            ['id' => '2', 'name' => '  Jane Doe', 'email' => '  janedoe@email.com'],
        ];

        $transformer = new Trim;

        $transformer->options(['type' => 'right']);

        $this->assertEquals($expected, array_map($transformer->transform(), $this->data));
    }

    /** @test */
    public function trim_left()
    {
        $expected = [
            ['id' => '1', 'name' => 'John Doe  ', 'email' => 'johndoe@email.com '],
            ['id' => '2 ', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com  '],
        ];

        $transformer = new Trim;

        $transformer->options(['type' => 'left']);

        $this->assertEquals($expected, array_map($transformer->transform(), $this->data));
    }

    /** @test */
    public function custom_character_mask()
    {
        $expected = [
            ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email'],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email'],
        ];

        $transformer = new Trim;

        $transformer->options(['mask' => ' cmo.']);

        $this->assertEquals($expected, array_map($transformer->transform(), $this->data));
    }

    /** @test */
    public function throws_an_exception_for_unsupported_trim_type()
    {
        $transformer = new Trim;

        $transformer->options(['type' => 'invalid']);

        $this->expectException('InvalidArgumentException');

        $transformer->transform();
    }
}

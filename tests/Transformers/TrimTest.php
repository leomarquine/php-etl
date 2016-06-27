<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Metis\Metis;

class TrimTest extends TestCase
{
    protected $source = [
        ['id' => ' 1', 'name' => 'John Doe  ', 'email' => ' johndoe@email.com '],
        ['id' => '2 ', 'name' => '  Jane Doe', 'email' => '  janedoe@email.com  '],
    ];

    /** @test */
    function trim_all_columns()
    {
        $results = Metis::extract('array', $this->source)
            ->transform('trim')
            ->get();

        $this->assertEquals($this->users, $results);
    }

    /** @test */
    function trim_specific_columns()
    {
        $columns = ['id', 'name'];

        $results = Metis::extract('array', $this->source)
            ->transform('trim', $columns)
            ->get();

        $expected = [
            ['id' => '1', 'name' => 'John Doe', 'email' => ' johndoe@email.com '],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => '  janedoe@email.com  '],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function trim_right()
    {
        $results = Metis::extract('array', $this->source)
            ->transform('trim', null, ['type' => 'right'])
            ->get();

        $expected = [
            ['id' => ' 1', 'name' => 'John Doe', 'email' => ' johndoe@email.com'],
            ['id' => '2', 'name' => '  Jane Doe', 'email' => '  janedoe@email.com'],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function trim_left()
    {
        $results = Metis::extract('array', $this->source)
            ->transform('trim', null, ['type' => 'left'])
            ->get();

        $expected = [
            ['id' => '1', 'name' => 'John Doe  ', 'email' => 'johndoe@email.com '],
            ['id' => '2 ', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com  '],
        ];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function trim_with_custom_character_mask()
    {
        $results = Metis::extract('array', $this->source)
            ->transform('trim', null, ['mask' => ' cmo.'])
            ->get();

        $expected = [
            ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email'],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email'],
        ];

        $this->assertEquals($expected, $results);
    }
}

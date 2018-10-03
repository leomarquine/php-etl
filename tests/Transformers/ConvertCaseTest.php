<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Etl\Transformers\ConvertCase;

class ConvertCaseTest extends TestCase
{
    protected $data = [
        ['id' => '1', 'name' => 'jane doe', 'email' => 'janedoe@email.com'],
        ['id' => '2', 'name' => 'JOHN DOE', 'email' => 'JOHNDOE@EMAIL.COM'],
    ];

    /** @test */
    public function lowercase()
    {
        $expected = [
            ['id' => '1', 'name' => 'jane doe', 'email' => 'janedoe@email.com'],
            ['id' => '2', 'name' => 'john doe', 'email' => 'johndoe@email.com'],
        ];

        $transformer = new ConvertCase;

        $transformer->options(['mode' => 'lower']);

        $this->assertEquals($expected, array_map($transformer->transform(), $this->data));
    }

    /** @test */
    public function uppercase()
    {
        $expected = [
            ['id' => '1', 'name' => 'JANE DOE', 'email' => 'JANEDOE@EMAIL.COM'],
            ['id' => '2', 'name' => 'JOHN DOE', 'email' => 'JOHNDOE@EMAIL.COM'],
        ];

        $transformer = new ConvertCase;

        $transformer->options(['mode' => 'upper']);

        $this->assertEquals($expected, array_map($transformer->transform(), $this->data));
    }

    /** @test */
    public function titlecase()
    {
        $expected = [
            ['id' => '1', 'name' => 'Jane Doe', 'email' => 'Janedoe@email.com'],
            ['id' => '2', 'name' => 'John Doe', 'email' => 'Johndoe@email.com'],
        ];

        $transformer = new ConvertCase;

        $transformer->options(['mode' => 'title']);

        $this->assertEquals($expected, array_map($transformer->transform(), $this->data));
    }

    /** @test */
    public function custom_columns()
    {
        $expected = [
            ['id' => '1', 'name' => 'jane doe', 'email' => 'janedoe@email.com'],
            ['id' => '2', 'name' => 'john doe', 'email' => 'JOHNDOE@EMAIL.COM'],
        ];

        $transformer = new ConvertCase;

        $transformer->options(['columns' => ['name']]);

        $this->assertEquals($expected, array_map($transformer->transform(), $this->data));
    }
}

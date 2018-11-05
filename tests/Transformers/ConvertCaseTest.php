<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Etl\Row;
use Marquine\Etl\Transformers\ConvertCase;

class ConvertCaseTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->data = [
            new Row(['id' => '1', 'name' => 'jane doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '2', 'name' => 'JOHN DOE', 'email' => 'JOHNDOE@EMAIL.COM']),
        ];
    }

    /** @test */
    public function lowercase()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'jane doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '2', 'name' => 'john doe', 'email' => 'johndoe@email.com']),
        ];

        $transformer = new ConvertCase;

        $transformer->options(['mode' => 'lower']);

        $transformer->initialize();

        array_map([$transformer, 'transform'], $this->data);

        $this->assertEquals($expected, $this->data);
    }

    /** @test */
    public function uppercase()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'JANE DOE', 'email' => 'JANEDOE@EMAIL.COM']),
            new Row(['id' => '2', 'name' => 'JOHN DOE', 'email' => 'JOHNDOE@EMAIL.COM']),
        ];

        $transformer = new ConvertCase;

        $transformer->options(['mode' => 'upper']);

        $transformer->initialize();

        array_map([$transformer, 'transform'], $this->data);

        $this->assertEquals($expected, $this->data);
    }

    /** @test */
    public function titlecase()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'Janedoe@email.com']),
            new Row(['id' => '2', 'name' => 'John Doe', 'email' => 'Johndoe@email.com']),
        ];

        $transformer = new ConvertCase;

        $transformer->options(['mode' => 'title']);

        $transformer->initialize();

        array_map([$transformer, 'transform'], $this->data);

        $this->assertEquals($expected, $this->data);
    }

    /** @test */
    public function custom_columns()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'jane doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '2', 'name' => 'john doe', 'email' => 'JOHNDOE@EMAIL.COM']),
        ];

        $transformer = new ConvertCase;

        $transformer->options(['columns' => ['name']]);

        $transformer->initialize();

        array_map([$transformer, 'transform'], $this->data);

        $this->assertEquals($expected, $this->data);
    }
}

<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Etl\Row;
use Marquine\Etl\Transformers\Pad;

class PasTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->data = [
            new Row(['id' => '1', 'name' => 'Jane Doe']),
        ];
    }

    /** @test */
    public function pad_a_string_with_default_options()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'Jane Doe    ']),
        ];

        $transformer = new Pad;

        $transformer->options([
            'columns' => ['name'],
            'length' => 12,
        ]);

        $this->execute($transformer, $this->data);

        $this->assertEquals($expected, $this->data);
    }

    /** @test */
    public function pad_a_string_with_a_custom_string()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'Jane Doe####']),
        ];

        $transformer = new Pad;

        $transformer->options([
            'columns' => ['name'],
            'length' => 12,
            'string' => '#',
        ]);

        $this->execute($transformer, $this->data);

        $this->assertEquals($expected, $this->data);
    }

    /** @test */
    public function pad_a_string_on_the_right_side()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'Jane Doe    ']),
        ];

        $transformer = new Pad;

        $transformer->options([
            'columns' => ['name'],
            'length' => 12,
            'type' => 'right',
        ]);

        $this->execute($transformer, $this->data);

        $this->assertEquals($expected, $this->data);
    }

    /** @test */
    public function pad_a_string_on_the_left_side()
    {
        $expected = [
            new Row(['id' => '1', 'name' => '    Jane Doe']),
        ];

        $transformer = new Pad;

        $transformer->options([
            'columns' => ['name'],
            'length' => 12,
            'type' => 'left',
        ]);

        $this->execute($transformer, $this->data);

        $this->assertEquals($expected, $this->data);
    }

    /** @test */
    public function pad_a_string_on_both_sides()
    {
        $expected = [
            new Row(['id' => '1', 'name' => '  Jane Doe  ']),
        ];

        $transformer = new Pad;

        $transformer->options([
            'columns' => ['name'],
            'length' => 12,
            'type' => 'both',
        ]);

        $this->execute($transformer, $this->data);

        $this->assertEquals($expected, $this->data);
    }
}

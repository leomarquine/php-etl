<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Etl\Row;
use Marquine\Etl\Transformers\NumberFormat;

class NumberFormatTest extends TestCase
{
    /** @test */
    public function number_format_with_default_options()
    {
        $data = [
            new Row(['id' => '1', 'value' => '12345']),
            new Row(['id' => '12', 'value' => '12345.12']),
        ];

        $expected = [
            new Row(['id' => '1', 'value' => '12,345']),
            new Row(['id' => '12', 'value' => '12,345']),
        ];

        $transformer = new NumberFormat;

        $transformer->options([
            'columns' => ['value'],
        ]);

        $this->execute($transformer, $data);

        $this->assertEquals($expected, $data);
    }

    /** @test */
    public function number_format_with_custom_options()
    {
        $data = [
            new Row(['id' => '1', 'value' => '12345']),
            new Row(['id' => '12', 'value' => '12345.12']),
        ];

        $expected = [
            new Row(['id' => '1', 'value' => '12.345,00']),
            new Row(['id' => '12', 'value' => '12.345,12']),
        ];

        $transformer = new NumberFormat;

        $transformer->options([
            'columns' => ['value'],
            'decimals' => 2,
            'decimalPoint' => ',',
            'thousandsSeparator' => '.'
        ]);

        $this->execute($transformer, $data);

        $this->assertEquals($expected, $data);
    }
}

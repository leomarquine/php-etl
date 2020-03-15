<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Tests\Extractors;

use Tests\TestCase;
use Wizaplace\Etl\Extractors\Csv;
use Wizaplace\Etl\Row;

class CsvTest extends TestCase
{
    /** @test */
    public function default_options()
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Csv();

        $extractor->input(__DIR__ . '/../data/csv1.csv');

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function custom_delimiter_and_enclosure()
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Csv();

        $extractor->input(__DIR__ . '/../data/csv2.csv');
        $extractor->options(['delimiter' => ';', 'enclosure' => '"']);

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function filtering_columns()
    {
        $expected = [
            new Row(['id' => 1, 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Csv();

        $extractor->input(__DIR__ . '/../data/csv1.csv');
        $extractor->options(['columns' => ['id', 'email']]);

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function mapping_columns()
    {
        $expected = [
            new Row(['id' => 1, 'email_address' => 'johndoe@email.com']),
            new Row(['id' => 2, 'email_address' => 'janedoe@email.com']),
        ];

        $extractor = new Csv();

        $extractor->input(__DIR__ . '/../data/csv1.csv');
        $extractor->options(['columns' => ['id' => 'id', 'email' => 'email_address']]);

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function custom_columns_indexes_when_there_is_no_header()
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Csv();

        $extractor->input(__DIR__ . '/../data/csv3.csv');
        $extractor->options(['columns' => ['id' => 1, 'name' => 2, 'email' => 3]]);

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    public function testMultiLineCsv(): void
    {
        $expected = [
            new Row(
                [
                    'id' => '1',
                    'name' => 'John

Doe',
                    'email' => 'johndoe@email.com'
                ]
            ),
            new Row(
                [
                    'id' => '2',
                    'name' => 'Jane Doe',
                    'email' => 'mail:
janedoe@email.com'
                ]
            ),
        ];

        $extractor = new Csv();
        $extractor->input(__DIR__ . '/../data/multiline.csv');

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }
}

<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\Csv;

class CsvTest extends TestCase
{
    /** @test */
    public function default_options()
    {
        $expected = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $extractor = new Csv;

        $extractor->extract(__DIR__ . '/../data/csv1.csv');

        $this->assertEquals($expected, iterator_to_array($extractor));
    }

    /** @test */
    public function custom_delimiter_and_enclosure()
    {
        $expected = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $extractor = new Csv;

        $extractor->options([
            'delimiter' => ';',
            'enclosure' => '"',
        ]);

        $extractor->extract(__DIR__ . '/../data/csv2.csv');

        $this->assertEquals($expected, iterator_to_array($extractor));
    }

    /** @test */
    public function filtering_columns()
    {
        $expected = [
            ['id' => 1, 'email' => 'johndoe@email.com'],
            ['id' => 2, 'email' => 'janedoe@email.com'],
        ];

        $extractor = new Csv;

        $extractor->options(['columns' => ['id', 'email']]);

        $extractor->extract(__DIR__ . '/../data/csv1.csv');

        $this->assertEquals($expected, iterator_to_array($extractor));
    }

    /** @test */
    public function mapping_columns()
    {
        $expected = [
            ['id' => 1, 'email_address' => 'johndoe@email.com'],
            ['id' => 2, 'email_address' => 'janedoe@email.com'],
        ];

        $extractor = new Csv;

        $extractor->options(['columns' => ['id' => 'id', 'email' => 'email_address']]);

        $extractor->extract(__DIR__ . '/../data/csv1.csv');

        $this->assertEquals($expected, iterator_to_array($extractor));
    }

    /** @test */
    public function custom_columns_indexes_when_there_is_no_header()
    {
        $expected = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $extractor = new Csv;

        $extractor->options(['columns' => ['id' => 1, 'name' => 2, 'email' => 3]]);

        $extractor->extract(__DIR__ . '/../data/csv3.csv');

        $this->assertEquals($expected, iterator_to_array($extractor));
    }
}

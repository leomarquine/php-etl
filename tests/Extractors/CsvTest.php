<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\Csv;

class CsvTest extends TestCase
{
    /** @test */
    public function default_options()
    {
        $extractor = new Csv;

        $extractor->source(__DIR__ . '/../data/csv1.csv');

        $expected = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $this->assertEquals($expected, iterator_to_array($extractor));
    }

    /** @test */
    public function custom_delimiter_and_enclosure()
    {
        $extractor = new Csv;

        $extractor->delimiter = ';';
        $extractor->enclosure = '"';

        $extractor->source(__DIR__ . '/../data/csv2.csv');

        $expected = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $this->assertEquals($expected, iterator_to_array($extractor));
    }

    /** @test */
    public function columns_filter()
    {
        $extractor = new Csv;

        $extractor->columns = ['id', 'email'];

        $extractor->source(__DIR__ . '/../data/csv1.csv');

        $expected = [
            ['id' => 1, 'email' => 'johndoe@email.com'],
            ['id' => 2, 'email' => 'janedoe@email.com'],
        ];

        $this->assertEquals($expected, iterator_to_array($extractor));
    }

    /** @test */
    public function columns_map()
    {
        $extractor = new Csv;

        $extractor->columns = [
            'id' => 'id',
            'email' => 'email_address',
        ];

        $extractor->source(__DIR__ . '/../data/csv1.csv');

        $expected = [
            ['id' => 1, 'email_address' => 'johndoe@email.com'],
            ['id' => 2, 'email_address' => 'janedoe@email.com'],
        ];

        $this->assertEquals($expected, iterator_to_array($extractor));
    }

    /** @test */
    public function columns_configuration_when_header_is_missing()
    {
        $extractor = new Csv;

        $extractor->columns = ['id' => 1, 'name' => 2, 'email' => 3];

        $extractor->source(__DIR__ . '/../data/csv3.csv');

        $expected = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $this->assertEquals($expected, iterator_to_array($extractor));
    }
}

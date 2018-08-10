<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\Csv;

class CsvTest extends TestCase
{
    protected $expected = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    public function extracts_data_from_a_csv_file_with_default_options()
    {
        $extractor = new Csv;

        $extractor->source(__DIR__.'/../data/csv1.csv');

        $this->assertEquals($this->expected, iterator_to_array($extractor));
    }

    /** @test */
    public function extracts_data_from_a_csv_file_with_custom_options()
    {
        $extractor = new Csv;

        $extractor->delimiter = ';';
        $extractor->enclosure = '"';

        $extractor->source(__DIR__.'/../data/csv2.csv');

        $this->assertEquals($this->expected, iterator_to_array($extractor));
    }

    /** @test */
    public function extracts_data_from_a_csv_file_without_a_header_line()
    {
        $extractor = new Csv;

        $extractor->columns = ['id' => 1, 'name' => 2, 'email' => 3];

        $extractor->source(__DIR__.'/../data/csv3.csv');

        $this->assertEquals($this->expected, iterator_to_array($extractor));
    }
}

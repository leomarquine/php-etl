<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\Csv;

class CsvTest extends TestCase
{
    private $expected = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    function extracts_data_from_a_csv_file()
    {
        $extractor = new Csv;

        $results = $extractor->extract('csv1.csv');

        $this->assertEquals($this->expected, iterator_to_array($results));
    }

    /** @test */
    function extracts_data_from_a_csv_file_with_custom_options()
    {
        $extractor = new Csv;

        $extractor->delimiter = ';';

        $extractor->enclosure = '"';

        $results = $extractor->extract('csv2.csv');

        $this->assertEquals($this->expected, iterator_to_array($results));
    }

    /** @test */
    function it_extracts_data_from_a_csv_file_without_a_header_line()
    {
        $extractor = new Csv;

        $extractor->columns = ['id' => 1, 'name' => 2, 'email' => 3];

        $results = $extractor->extract('csv3.csv');

        $this->assertEquals($this->expected, iterator_to_array($results));
    }
}

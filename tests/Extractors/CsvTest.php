<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Metis\Extractors\Csv;

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

        $results = $extractor->extract('users.csv');

        $this->assertEquals($this->expected, $results);
    }

    /** @test */
    function extracts_data_from_a_csv_file_with_custom_options()
    {
        $options = ['delimiter' => ',', 'enclosure' => "'"];

        $extractor = new Csv($options);

        $results = $extractor->extract('users_custom_options.csv');

        $this->assertEquals($this->expected, $results);
    }

    /** @test */
    function it_extracts_data_from_a_csv_file_without_a_header_line()
    {
        $columns = ['id' => 1, 'name' => 2, 'email' => 3];

        $extractor = new Csv;

        $results = $extractor->extract('users_noheader.csv', $columns);

        $this->assertEquals($this->expected, $results);
    }
}

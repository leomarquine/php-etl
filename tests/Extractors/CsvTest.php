<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Metis\Metis;

class CsvTest extends TestCase
{
    private $expected = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    function extracts_data_from_a_csv_file()
    {
        $results = Metis::extract('csv', 'users.csv')->get();

        $this->assertEquals($this->expected, $results);
    }

    /** @test */
    function extracts_data_from_a_csv_file_with_custom_options()
    {
        $options = ['delimiter' => ',', 'enclosure' => "'"];

        $results = Metis::extract('csv', 'users_custom_options.csv', null, $options)->get();

        $this->assertEquals($this->expected, $results);
    }

    /** @test */
    function it_extracts_data_from_a_csv_file_without_a_header_line()
    {
        $columns = ['id' => 1, 'name' => 2, 'email' => 3];

        $data = Metis::extract('csv', 'users_noheader.csv', $columns)->get();

        $this->assertEquals($this->expected, $data);
    }
}

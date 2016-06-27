<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Metis\Metis;

class XmlTest extends TestCase
{
    private $expected = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    function extracts_data_from_a_xml_file()
    {
        $results = Metis::extract('xml', 'users.xml', null, ['loop' => '/users/user'])->get();

        $this->assertEquals($this->expected, $results);
    }

    /** @test */
    function extracts_data_from_a_xml_file_with_custom_columns_path()
    {
        $columns = [
            'id' => 'id/value',
            'name' => 'name/value',
            'email' => 'email/value'
        ];

        $results = Metis::extract('xml', 'users_path.xml', $columns, ['loop' => '/users/user'])->get();

        $this->assertEquals($this->expected, $results);
    }
}

<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Metis\Extractors\Xml;

class XmlTest extends TestCase
{
    private $expected = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    function extracts_data_from_a_xml_file()
    {
        $options = ['loop' => '/users/user'];

        $extractor = new Xml($options);

        $results = $extractor->extract('users.xml');

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

        $options = ['loop' => '/users/user'];

        $extractor = new Xml($options);

        $results = $extractor->extract('users_path.xml', $columns);

        $this->assertEquals($this->expected, $results);
    }
}

<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\Xml;

class XmlTest extends TestCase
{
    private $expected = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    function extracts_data_from_a_xml_file()
    {
        $extractor = new Xml;

        $extractor->loop = '/users/user';

        $results = $extractor->extract('xml1.xml');

        $this->assertEquals($this->expected, iterator_to_array($results));
    }

    /** @test */
    function extracts_data_from_a_xml_file_with_custom_columns_path()
    {
        $extractor = new Xml;

        $extractor->loop = '/users/user';

        $extractor->columns = [
            'id' => 'id/value',
            'name' => 'name/value',
            'email' => 'email/value'
        ];

        $results = $extractor->extract('xml2.xml');

        $this->assertEquals($this->expected, iterator_to_array($results));
    }
}

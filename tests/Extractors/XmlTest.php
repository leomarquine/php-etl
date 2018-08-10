<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\Xml;

class XmlTest extends TestCase
{
    protected $expected = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    public function extracts_data_from_an_xml_file_retrieving_all_fields_within_the_loop_path()
    {
        $extractor = new Xml;

        $extractor->loop = '/users/user';

        $extractor->source(__DIR__ . '/../data/xml1.xml');

        $this->assertEquals($this->expected, iterator_to_array($extractor));
    }

    /** @test */
    public function extracts_data_from_an_xml_file_retrieving_custom_fields_within_the_loop_path()
    {
        $extractor = new Xml;

        $extractor->loop = '/users/user';

        $extractor->columns = [
            'id' => '/@id',
            'name' => '/profile/name',
            'email' => '/profile/email/@value',
        ];

        $extractor->source(__DIR__ . '/../data/xml2.xml');

        $this->assertEquals($this->expected, iterator_to_array($extractor));
    }
}

<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Row;
use Marquine\Etl\Extractors\Xml;

class XmlTest extends TestCase
{
    /** @test */
    public function custom_loop_path()
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Xml;

        $extractor->input(__DIR__ . '/../data/xml1.xml');
        $extractor->options(['loop' => '/users/user']);


        $this->assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function custom_fields_within_the_loop_path()
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Xml;

        $extractor->input(__DIR__ . '/../data/xml2.xml');
        $extractor->options([
            'loop' => '/users/user',
            'columns' => [
                'id' => '/@id',
                'name' => '/profile/name',
                'email' => '/profile/email/@value',
            ],
        ]);

        $this->assertEquals($expected, iterator_to_array($extractor->extract()));
    }
}

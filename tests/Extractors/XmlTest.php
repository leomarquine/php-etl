<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\Xml;

class XmlTest extends TestCase
{
    /** @test */
    public function custom_loop_path()
    {
        $expected = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $extractor = new Xml;

        $extractor->options(['loop' => '/users/user']);

        $extractor->extract(__DIR__ . '/../data/xml1.xml');

        $this->assertEquals($expected, iterator_to_array($extractor));
    }

    /** @test */
    public function custom_fields_within_the_loop_path()
    {
        $expected = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $extractor = new Xml;

        $extractor->options([
            'loop' => '/users/user',
            'columns' => [
                'id' => '/@id',
                'name' => '/profile/name',
                'email' => '/profile/email/@value',
            ],
        ]);

        $extractor->extract(__DIR__ . '/../data/xml2.xml');

        $this->assertEquals($expected, iterator_to_array($extractor));
    }
}

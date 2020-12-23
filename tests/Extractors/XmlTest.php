<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Extractors;

use Tests\TestCase;
use Wizaplace\Etl\Extractors\Xml;
use Wizaplace\Etl\Row;

class XmlTest extends TestCase
{
    /** @test */
    public function customLoopPath(): void
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Xml();

        $extractor->input(__DIR__ . '/../data/xml1.xml');
        $extractor->options(['loop' => '/users/user']);

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function customFieldsWithinTheLoopPath(): void
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Xml();

        $extractor->input(__DIR__ . '/../data/xml2.xml');
        $extractor->options([
            'loop' => '/users/user',
            'columns' => [
                'id' => '/@id',
                'name' => '/profile/name',
                'email' => '/profile/email/@value',
            ],
        ]);

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }
}

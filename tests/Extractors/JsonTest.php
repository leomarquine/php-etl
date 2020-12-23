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
use Wizaplace\Etl\Extractors\Json;
use Wizaplace\Etl\Row;

class JsonTest extends TestCase
{
    /** @test */
    public function defaultOptions(): void
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Json();

        $extractor->input(__DIR__ . '/../data/json1.json');

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function customColumnsJsonPath(): void
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Json();

        $extractor->input(__DIR__ . '/../data/json2.json');
        $extractor->options(['columns' => [
            'id' => '$..bindings[*].id.value',
            'name' => '$..bindings[*].name.value',
            'email' => '$..bindings[*].email.value',
        ]]);

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }
}

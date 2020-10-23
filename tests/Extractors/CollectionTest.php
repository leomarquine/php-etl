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
use Wizaplace\Etl\Extractors\Collection;
use Wizaplace\Etl\Row;

class CollectionTest extends TestCase
{
    protected $input = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    public function default_options()
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Collection();

        $extractor->input($this->input);

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function custom_columns()
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe']),
            new Row(['id' => 2, 'name' => 'Jane Doe']),
        ];

        $extractor = new Collection();

        $extractor->input($this->input)->options(['columns' => ['id', 'name']]);

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }
}

<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\Collection;

class CollectionTest extends TestCase
{
    protected $source = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    public function default_options()
    {
        $expected = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $extractor = new Collection;

        $iterator = $extractor->extract($this->source);

        $this->assertEquals($expected, iterator_to_array($iterator));
    }

    /** @test */
    public function custom_columns()
    {
        $expected = [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Doe'],
        ];

        $extractor = new Collection;

        $extractor->options(['columns' => ['id', 'name']]);

        $iterator = $extractor->extract($this->source);

        $this->assertEquals($expected, iterator_to_array($iterator));
    }
}

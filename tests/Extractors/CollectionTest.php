<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Row;
use Marquine\Etl\Extractors\Collection;

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

        $extractor = new Collection;

        $extractor->input($this->input);

        $this->assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function custom_columns()
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe']),
            new Row(['id' => 2, 'name' => 'Jane Doe']),
        ];

        $extractor = new Collection;

        $extractor->input($this->input)->options(['columns' => ['id', 'name']]);

        $this->assertEquals($expected, iterator_to_array($extractor->extract()));
    }
}

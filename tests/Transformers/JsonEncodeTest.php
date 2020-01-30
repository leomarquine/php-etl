<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Tests\Transformers;

use Tests\TestCase;
use Wizaplace\Etl\Row;
use Wizaplace\Etl\Transformers\JsonEncode;

class JsonEncodeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->data = [
            new Row(['id' => '1', 'data' => ['name' => 'John Doe', 'email' => 'johndoe@email.com']]),
            new Row(['id' => '2', 'data' => ['name' => 'Jane Doe', 'email' => 'janedoe@email.com']]),
        ];
    }

    /** @test */
    public function default_options()
    {
        $expected = [
            new Row(['id' => '"1"', 'data' => '{"name":"John Doe","email":"johndoe@email.com"}']),
            new Row(['id' => '"2"', 'data' => '{"name":"Jane Doe","email":"janedoe@email.com"}']),
        ];

        $transformer = new JsonEncode();

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function custom_columns()
    {
        $expected = [
            new Row(['id' => '1', 'data' => '{"name":"John Doe","email":"johndoe@email.com"}']),
            new Row(['id' => '2', 'data' => '{"name":"Jane Doe","email":"janedoe@email.com"}']),
        ];

        $transformer = new JsonEncode();

        $transformer->options(['columns' => ['data']]);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }
}

<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Transformers;

use Tests\TestCase;
use Wizaplace\Etl\Row;
use Wizaplace\Etl\Transformers\JsonDecode;

class JsonDecodeTest extends TestCase
{
    /**
     * Row array to be transformed in testing.
     *
     * @var Row[]
     */
    private $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = [
            new Row(['id' => '"1"', 'data' => '{"name":"John Doe","email":"johndoe@email.com"}']),
            new Row(['id' => '"2"', 'data' => '{"name":"Jane Doe","email":"janedoe@email.com"}']),
        ];
    }

    /** @test */
    public function defaultOptions(): void
    {
        $expected = [
            new Row(['id' => '1', 'data' => (object) ['name' => 'John Doe', 'email' => 'johndoe@email.com']]),
            new Row(['id' => '2', 'data' => (object) ['name' => 'Jane Doe', 'email' => 'janedoe@email.com']]),
        ];

        $transformer = new JsonDecode();

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function convertingObjectsToAssociativeArrays(): void
    {
        $expected = [
            new Row(['id' => '1', 'data' => ['name' => 'John Doe', 'email' => 'johndoe@email.com']]),
            new Row(['id' => '2', 'data' => ['name' => 'Jane Doe', 'email' => 'janedoe@email.com']]),
        ];

        $transformer = new JsonDecode();

        $transformer->options(['assoc' => true]);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function customColumns(): void
    {
        $expected = [
            new Row(['id' => '"1"', 'data' => (object) ['name' => 'John Doe', 'email' => 'johndoe@email.com']]),
            new Row(['id' => '"2"', 'data' => (object) ['name' => 'Jane Doe', 'email' => 'janedoe@email.com']]),
        ];

        $transformer = new JsonDecode();

        $transformer->options(['columns' => ['data']]);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }
}

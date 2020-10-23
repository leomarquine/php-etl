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
use Wizaplace\Etl\Transformers\Replace;

class ReplaceTest extends TestCase
{
    /**
     * Row array to be transformed in testing.
     *
     * @var Row[]
     */
    protected $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = [
            new Row(['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '3', 'name' => 'Jane Doe', 'email' => 'janeDoe@email.com']),
        ];
    }

    /** @test */
    public function default_type_option()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'John Die', 'email' => 'johndoe@email.com']),
            new Row(['id' => '2', 'name' => 'Jane Die', 'email' => 'janedoe@email.com']),
            new Row(['id' => '3', 'name' => 'Jane Die', 'email' => 'janeDie@email.com']),
        ];

        $transformer = new Replace();

        $transformer->options(['search' => 'Doe', 'replace' => 'Die']);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function default_type_option_custom_columns()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'John Die', 'email' => 'johndoe@email.com']),
            new Row(['id' => '2', 'name' => 'Jane Die', 'email' => 'janedoe@email.com']),
            new Row(['id' => '3', 'name' => 'Jane Die', 'email' => 'janeDoe@email.com']),
        ];

        $transformer = new Replace();

        $transformer->options([
            'search' => 'Doe',
            'replace' => 'Die',
            'columns' => ['name'],
        ]);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function str_type_option()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'John Die', 'email' => 'johndoe@email.com']),
            new Row(['id' => '2', 'name' => 'Jane Die', 'email' => 'janedoe@email.com']),
            new Row(['id' => '3', 'name' => 'Jane Die', 'email' => 'janeDie@email.com']),
        ];

        $transformer = new Replace();

        $transformer->options([
            'type' => 'str',
            'search' => 'Doe',
            'replace' => 'Die',
        ]);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function str_type_option_custom_columns()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'John Die', 'email' => 'johndoe@email.com']),
            new Row(['id' => '2', 'name' => 'Jane Die', 'email' => 'janedoe@email.com']),
            new Row(['id' => '3', 'name' => 'Jane Die', 'email' => 'janeDoe@email.com']),
        ];

        $transformer = new Replace();

        $transformer->options([
            'type' => 'str',
            'search' => 'Doe',
            'replace' => 'Die',
            'columns' => ['name'],
        ]);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function preg_type_option()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'John Die', 'email' => 'johndoe@email.com']),
            new Row(['id' => '2', 'name' => 'Jane Die', 'email' => 'janedoe@email.com']),
            new Row(['id' => '3', 'name' => 'Jane Die', 'email' => 'janeDie@email.com']),
        ];

        $transformer = new Replace();

        $transformer->options([
            'type' => 'preg',
            'search' => '/Doe/m',
            'replace' => 'Die',
        ]);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function preg_type_option_custom_columns()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'John Die', 'email' => 'johndoe@email.com']),
            new Row(['id' => '2', 'name' => 'Jane Die', 'email' => 'janedoe@email.com']),
            new Row(['id' => '3', 'name' => 'Jane Die', 'email' => 'janeDoe@email.com']),
        ];

        $transformer = new Replace();

        $transformer->options([
            'type' => 'preg',
            'search' => '/Doe/m',
            'replace' => 'Die',
            'columns' => ['name'],
        ]);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function throws_an_exception_for_unsupported_replace_type()
    {
        $transformer = new Replace();

        $transformer->options(['type' => 'invalid']);

        $this->expectException('InvalidArgumentException');

        $this->execute($transformer, $this->data);
    }
}

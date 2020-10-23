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
use Wizaplace\Etl\Transformers\Trim;

class TrimTest extends TestCase
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
            new Row(['id' => ' 1', 'name' => 'John Doe  ', 'email' => ' johndoe@email.com ']),
            new Row(['id' => '2 ', 'name' => '  Jane Doe', 'email' => '  janedoe@email.com  ']),
        ];
    }

    /** @test */
    public function default_options()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $transformer = new Trim();

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function custom_columns()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'John Doe', 'email' => ' johndoe@email.com ']),
            new Row(['id' => '2', 'name' => 'Jane Doe', 'email' => '  janedoe@email.com  ']),
        ];

        $transformer = new Trim();

        $transformer->options(['columns' => ['id', 'name']]);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function trim_right()
    {
        $expected = [
            new Row(['id' => ' 1', 'name' => 'John Doe', 'email' => ' johndoe@email.com']),
            new Row(['id' => '2', 'name' => '  Jane Doe', 'email' => '  janedoe@email.com']),
        ];

        $transformer = new Trim();

        $transformer->options(['type' => 'right']);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function trim_left()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'John Doe  ', 'email' => 'johndoe@email.com ']),
            new Row(['id' => '2 ', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com  ']),
        ];

        $transformer = new Trim();

        $transformer->options(['type' => 'left']);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function custom_character_mask()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email']),
            new Row(['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email']),
        ];

        $transformer = new Trim();

        $transformer->options(['mask' => ' cmo.']);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function throws_an_exception_for_unsupported_trim_type()
    {
        $transformer = new Trim();

        $transformer->options(['type' => 'invalid']);

        $this->expectException('InvalidArgumentException');

        $this->execute($transformer, $this->data);
    }
}

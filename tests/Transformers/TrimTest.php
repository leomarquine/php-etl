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
    protected array $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = [
            new Row(['id' => ' 1', 'name' => 'John Doe  ', 'email' => ' johndoe@email.com ']),
            new Row(['id' => '2 ', 'name' => '  Jane Doe', 'email' => '  janedoe@email.com  ']),
        ];
    }

    /** @test */
    public function defaultOptions(): void
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
    public function customColumns(): void
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'John Doe', 'email' => ' johndoe@email.com ']),
            new Row(['id' => '2', 'name' => 'Jane Doe', 'email' => '  janedoe@email.com  ']),
        ];

        $transformer = new Trim();

        $transformer->options([$transformer::COLUMNS => ['id', 'name']]);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function trimRight(): void
    {
        $expected = [
            new Row(['id' => ' 1', 'name' => 'John Doe', 'email' => ' johndoe@email.com']),
            new Row(['id' => '2', 'name' => '  Jane Doe', 'email' => '  janedoe@email.com']),
        ];

        $transformer = new Trim();

        $transformer->options([$transformer::TYPE => 'right']);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function trimLeft(): void
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'John Doe  ', 'email' => 'johndoe@email.com ']),
            new Row(['id' => '2 ', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com  ']),
        ];

        $transformer = new Trim();

        $transformer->options([$transformer::TYPE => 'left']);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function customCharacterMask(): void
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email']),
            new Row(['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email']),
        ];

        $transformer = new Trim();

        $transformer->options([$transformer::MASK => ' cmo.']);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function throwsExceptionForUnsupportedTrimType(): void
    {
        $transformer = new Trim();

        $transformer->options([$transformer::TYPE => 'invalid']);

        $this->expectException('InvalidArgumentException');

        $this->execute($transformer, $this->data);
    }
}

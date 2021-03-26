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
use Wizaplace\Etl\Transformers\ConvertCase;

class ConvertCaseTest extends TestCase
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
            new Row(['id' => '1', 'name' => 'jane doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '2', 'name' => 'JOHN DOE', 'email' => 'JOHNDOE@EMAIL.COM']),
        ];
    }

    /** @test */
    public function lowercase(): void
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'jane doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '2', 'name' => 'john doe', 'email' => 'johndoe@email.com']),
        ];

        $transformer = new ConvertCase();

        $transformer->options([$transformer::MODE => 'lower']);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function uppercase(): void
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'JANE DOE', 'email' => 'JANEDOE@EMAIL.COM']),
            new Row(['id' => '2', 'name' => 'JOHN DOE', 'email' => 'JOHNDOE@EMAIL.COM']),
        ];

        $transformer = new ConvertCase();

        $transformer->options([$transformer::MODE => 'upper']);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function titleCase(): void
    {
        // @see https://www.php.net/manual/en/migration73.new-features.php
        if (phpversion() < 7.3) {
            $expected = [
                new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'Janedoe@email.com']),
                new Row(['id' => '2', 'name' => 'John Doe', 'email' => 'Johndoe@email.com']),
            ];
        } else {
            $expected = [
                new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'Janedoe@Email.com']),
                new Row(['id' => '2', 'name' => 'John Doe', 'email' => 'Johndoe@Email.com']),
            ];
        }

        $transformer = new ConvertCase();

        $transformer->options([$transformer::MODE => 'title']);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function customColumns(): void
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'jane doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '2', 'name' => 'john doe', 'email' => 'JOHNDOE@EMAIL.COM']),
        ];

        $transformer = new ConvertCase();
        $transformer->options([$transformer::COLUMNS => ['name']]);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }
}

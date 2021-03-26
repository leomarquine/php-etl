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
use Wizaplace\Etl\Transformers\UniqueRows;

class UniqueRowsTest extends TestCase
{
    /** @test */
    public function compareAllColumns(): void
    {
        $data = [
            new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $expected = $data;
        $expected[1]->discard();
        $expected[3]->discard();

        $transformer = new UniqueRows();

        $this->execute($transformer, $data);

        static::assertEquals($expected, $data);
    }

    /** @test */
    public function compareTheGivenColumns(): void
    {
        $data = [
            new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@email.org']),
            new Row(['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@email.net']),
        ];

        $expected = $data;
        $expected[1]->discard();
        $expected[3]->discard();

        $transformer = new UniqueRows();

        $transformer->options([$transformer::COLUMNS => ['id', 'name']]);

        $this->execute($transformer, $data);

        static::assertEquals($expected, $data);
    }

    /** @test */
    public function compareAllColumnsOfConsecutiveRows(): void
    {
        $data = [
            new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $expected = $data;
        $expected[1]->discard();
        $expected[2]->discard();
        $expected[4]->discard();

        $transformer = new UniqueRows();

        $transformer->options([$transformer::CONSECUTIVE => true]);

        $this->execute($transformer, $data);

        static::assertEquals($expected, $data);
    }

    /** @test */
    public function compareTheGivenColumnsOfConsecutiveRows(): void
    {
        $data = [
            new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@email.net']),
            new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@email.org']),
            new Row(['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@email.net']),
            new Row(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $expected = $data;
        $expected[1]->discard();
        $expected[2]->discard();
        $expected[4]->discard();

        $transformer = new UniqueRows();

        $transformer->options(
            [
                $transformer::CONSECUTIVE => true,
                $transformer::COLUMNS => ['id', 'name'],
            ]
        );

        $this->execute($transformer, $data);

        static::assertEquals($expected, $data);
    }
}

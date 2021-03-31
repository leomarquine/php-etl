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
use Wizaplace\Etl\Transformers\RowCallback;

class RowCallbackTest extends TestCase
{
    /** @test */
    public function rowCallbackAsValidator(): void
    {
        $validationClosure = function (Row $row): Row {
            return
                \filter_var(
                    $row->get('email'),
                    FILTER_VALIDATE_EMAIL
                )
                ? $row
                : $row->discard();
        };

        $data = [
            new Row(['id' => '1', 'name' => 'John Doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '3', 'name' => 'foo', 'email' => 'isnotavalidemail']),
            new Row(['id' => '4', 'name' => 'bar', 'email' => 'bar@email.com']),
        ];

        $expected = [
            new Row(['id' => '1', 'name' => 'John Doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
            (new Row(['id' => '3', 'name' => 'foo', 'email' => 'isnotavalidemail']))->discard(),
            new Row(['id' => '4', 'name' => 'bar', 'email' => 'bar@email.com']),
        ];

        $transformer = new RowCallback();
        $transformer->options(
            [
                $transformer::CALLBACK => $validationClosure,
            ]
        );

        $this->execute($transformer, $data);

        static::assertEquals(
            $expected,
            $data
        );
    }
}

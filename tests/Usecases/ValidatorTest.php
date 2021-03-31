<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Usecases;

use PHPUnit\Framework\TestCase;
use Wizaplace\Etl\Etl;
use Wizaplace\Etl\Extractors\Csv;
use Wizaplace\Etl\Row;
use Wizaplace\Etl\Transformers\RowCallback;
use Wizaplace\Etl\Transformers\Transformer;

class ValidatorTest extends TestCase
{
    /** @test */
    public function anonymousTransformerAsValidator(): void
    {
        // php-cs-fixer and phpcs disagree about this :|
        // phpcs:ignore
        $validator = new class() extends Transformer {
            public function transform(Row $row): void
            {
                if ('1' === $row->get('id')) {
                    $row->discard();
                }
            }
        };

        $actual = (new Etl())
            ->extract(
                new Csv(),
                __DIR__ . '/data/users.csv',
                [Csv::DELIMITER => ';']
            )
            ->transform($validator)
            ->toArray();

        static::assertEquals(
            [
                [
                    'id' => '2',
                    'name' => 'Jane Doe',
                    'email' => 'janedoe@email.com',
                ],
                [
                    'id' => '3',
                    'name' => 'Hello World',
                    'email' => 'hello@world.com',
                ],
            ],
            $actual
        );
    }

    /** @test */
    public function rowCallbackAsValidator(): void
    {
        $validator = function (Row $row): void {
            if ('1' === $row->get('id')) {
                $row->discard();
            }
        };

        $actual = (new Etl())
            ->extract(
                new Csv(),
                __DIR__ . '/data/users.csv',
                [Csv::DELIMITER => ';']
            )
            ->transform(
                new RowCallback(),
                [RowCallback::CALLBACK => $validator]
            )
            ->toArray();

        static::assertEquals(
            [
                [
                    'id' => '2',
                    'name' => 'Jane Doe',
                    'email' => 'janedoe@email.com',
                ],
                [
                    'id' => '3',
                    'name' => 'Hello World',
                    'email' => 'hello@world.com',
                ],
            ],
            $actual
        );
    }
}

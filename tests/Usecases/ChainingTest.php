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
use Wizaplace\Etl\Extractors\Aggregator;
use Wizaplace\Etl\Extractors\Csv;
use Wizaplace\Etl\Transformers\ConvertCase;
use Wizaplace\Etl\Transformers\RenameColumns;

class ChainingTest extends TestCase
{
    /**
     * Merging data from two different source
     * using a common matching column data
     *
     * @test
     */
    public function merging_iterators_chaining()
    {
        // lazy get users
        $usersIterator = (new Etl())
            ->extract(
                new Csv(),
                __DIR__ . '/data/users.csv',
                ['delimiter' => ';']
            )
            ->toIterator();

        // lazy get extended user info
        $infosIterator = (new Etl())
            ->extract(
                new Csv(),
                __DIR__ . '/data/infos.csv',
                ['delimiter' => ';']
            )
            ->transform(
                new RenameColumns(),
                [
                    'columns' => [
                        'courriel' => 'email',
                    ],
                ]
            )
            ->transform(
                new ConvertCase(),
                [
                    'columns' => ['email'],
                    'mode' => 'lower',
                ]
            )
            ->toIterator();

        // and finally lazy merge these iterators data
        $usersInfosIterator = (new Etl())
            ->extract(
                new Aggregator(),
                [
                    $usersIterator,
                    $infosIterator,
                ],
                [
                    'index' => ['email'],
                    'columns' => [
                        'id',
                        'email',
                        'name',
                        'age',
                    ],
                    'strict' => false,
                ]
            )
            ->toIterator();

        $expected = [
            [
                'id' => '1',
                'name' => 'John Doe',
                'email' => 'johndoe@email.com',
                'age' => '42',
            ],
            [
                'id' => '2',
                'name' => 'Jane Doe',
                'email' => 'janedoe@email.com',
                'age' => '39',
            ],
            [
                'id' => '3',
                'name' => 'Hello World',
                'email' => 'hello@world.com',
            ],
            [
                'age' => '1000',
                'email' => 'glinglin@email.com',
            ],
        ];

        $actual = iterator_to_array(
            $usersInfosIterator
        );

        static::assertSame(
            $expected,
            $actual
        );
    }
}

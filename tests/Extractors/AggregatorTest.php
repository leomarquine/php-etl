<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Extractors;

use Tests\TestCase;
use Wizaplace\Etl\Exception\IncompleteDataException;
use Wizaplace\Etl\Exception\InvalidOptionException;
use Wizaplace\Etl\Extractors\Aggregator;
use Wizaplace\Etl\Row;

class AggregatorTest extends TestCase
{
    /**
     * @throws IncompleteDataException
     *
     * @test
     * @dataProvider invalidOptionsProvider
     */
    public function invalidIndexOptions(array $invalidOptions, int $exceptionCode): void
    {
        $extractor = new Aggregator();

        $extractor
            ->input(
                $this->iteratorsProvider()[0][0] // 👀️
            )
            ->options(
                \array_merge(
                    $invalidOptions,
                    [$extractor::STRICT => false]
                )
            );

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionCode($exceptionCode);

        iterator_to_array($extractor->extract());
    }

    /**
     * @test
     *
     * @dataProvider iteratorsProvider
     **/
    public function strictIndexMatching(array $iterators): void
    {
        $extractor = new Aggregator();

        $extractor
            ->input($iterators)
            ->options(
                [
                    $extractor::INDEX => ['email'],
                    $extractor::COLUMNS => ['name', 'twitter'],
                    $extractor::STRICT => true,
                ]
            );

        $this->expectException(IncompleteDataException::class);
        $this->expectExceptionMessage('2 rows were rejected because incomplete');

        \iterator_to_array($extractor->extract());
    }

    /**
     * @test
     *
     * @dataProvider iteratorsProvider
     **/
    public function unstrictIndexMatching(array $iterators): void
    {
        $extractor = new Aggregator();

        $extractor
            ->input($iterators)
            ->options(
                [
                    $extractor::INDEX => ['email'],
                    $extractor::COLUMNS => ['name', 'twitter'],
                    $extractor::STRICT => false,
                    $extractor::DISCARD => false,
                ]
            );

        $actual = \iterator_to_array($extractor->extract(), false);
        $expected = [
            new Row([
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'johndoe@email.com',
                'twitter' => '@john',
            ]),
            new Row([
                'id' => 2,
                'name' => 'Jane Doe',
                'email' => 'janedoe@email.com',
                'twitter' => '@jane',
            ]),
            (new Row([
                'id' => 3,
                'name' => 'Incomplete1',
                'email' => 'incomplete1@dirtydata',
                'twitter' => null,
            ]))->setIncomplete(),
            (new Row([
                'id' => 4,
                'name' => 'Incomplete2',
                'email' => 'incomplete2@dirtydata',
                'twitter' => null,
            ]))->setIncomplete(),
        ];

        static::assertEquals($expected, $actual);
    }

    /**
     * @test
     *
     * @dataProvider iteratorsProvider
     **/
    public function discardIncompleteRowIndexMatching(array $iterators): void
    {
        $extractor = new Aggregator();

        $extractor
            ->input($iterators)
            ->options(
                [
                    $extractor::INDEX => ['email'],
                    $extractor::COLUMNS => ['name', 'twitter'],
                    $extractor::STRICT => false,
                    $extractor::DISCARD => true,
                ]
            );

        $actual = \iterator_to_array($extractor->extract(), false);
        $expected = [
            new Row([
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'johndoe@email.com',
                'twitter' => '@john',
            ]),
            new Row([
                'id' => 2,
                'name' => 'Jane Doe',
                'email' => 'janedoe@email.com',
                'twitter' => '@jane',
            ]),
        ];

        static::assertEquals($expected, $actual);
    }

    /** @test */
    public function bigShuffledDataSet(): void
    {
        $expected = 10 ** 4;

        $iterator = function (string $key, string $template) use ($expected): \Generator {
            $ids = \range(1, $expected);
            \shuffle($ids);
            foreach ($ids as $id) {
                yield [
                    'id' => $id,
                    "useseless_$id" => 'nevermind',
                    $key => sprintf($template, $id),
                ];
            }
        };

        $extractor = new Aggregator();

        $extractor
            ->input(
                [
                    $iterator('email', 'user_%s@email.com'),
                    $iterator('name', 'name_%s'),
                    $iterator('info', 'info_%s'),
                    $iterator('stuff', 'stuff_%s'),
                ]
            )
            ->options(
                [
                    $extractor::INDEX => ['id'],
                    $extractor::COLUMNS => ['email', 'name', 'info', 'stuff'],
                ]
            );

        $actual = 0;
        $generator = $extractor->extract();
        while ($generator->valid()) {
            $generator->next();
            $actual++;
        }

        static::assertEquals($expected, $actual);
    }

    public function invalidOptionsProvider(): array
    {
        return [
            'invalid index' => [
                [
                    Aggregator::COLUMNS => ['name', 'id'],
                ],
                'error_code' => 1,
            ],
            'invalid columns' => [
                [
                    Aggregator::INDEX => ['email'],
                ],
                'error_code' => 2,
            ],
        ];
    }

    public function iteratorsProvider(): array
    {
        $simpleDataSet =
            [
                [
                    $this->arrayToIterator([
                        ['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
                        [], // should not happen
                        ['impossible error'], // should not happen as well
                        ['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
                        ['id' => 3, 'name' => 'Incomplete1', 'email' => 'incomplete1@dirtydata'],
                        ['id' => 4, 'name' => 'Incomplete2', 'email' => 'incomplete2@dirtydata'],
                    ]),
                    $this->arrayToIterator([
                        ['email' => 'janedoe@email.com', 'twitter' => '@jane'],
                        ['email' => 'johndoe@email.com', 'twitter' => '@john'],
                        ['impossible error'], // should not happen as well
                    ]),
                ],
            ];

        return [
            $simpleDataSet,
        ];
    }

    public function arrayToIterator(array $lines): \Iterator
    {
        foreach ($lines as $line) {
            yield $line;
        }
    }
}

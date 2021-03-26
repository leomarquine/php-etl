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
use Wizaplace\Etl\Exception\InvalidInputException;
use Wizaplace\Etl\Exception\IoException;
use Wizaplace\Etl\Extractors\Csv;
use Wizaplace\Etl\Row;

class CsvTest extends TestCase
{
    /** @test */
    public function defaultOptions(): void
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Csv();

        $extractor->input(__DIR__ . '/../data/simple.csv');

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function customDelimiterAndEnclosure(): void
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Csv();

        $extractor->input(__DIR__ . '/../data/quoted.csv');
        $extractor->options(
            [
                $extractor::DELIMITER => '|',
                $extractor::ENCLOSURE => '#',
            ]
        );

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function filteringColumns(): void
    {
        $expected = [
            new Row(['id' => 1, 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Csv();

        $extractor->input(__DIR__ . '/../data/simple.csv');
        $extractor->options(
            [
                $extractor::COLUMNS => ['id', 'email'],
            ]
        );

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function filteringColumnsWithMoreAskedColumnsThanReallyAvailable(): void
    {
        $extractor = new Csv();
        $extractor->input(__DIR__ . '/../data/simple.csv');

        // Without error handling (no BC). No message or error is expected.
        $extractor->options(
            [
                $extractor::COLUMNS => ['id', 'email', 'foo', 'bar'],
            ]
        );
        $data = [];
        foreach ($extractor->extract() as $row) {
            $data[] = $row;
        }

        // With error handling
        $extractor->options(
            [
                $extractor::COLUMNS => ['id', 'email', 'foo', 'bar'],
                $extractor::THROW_ERROR => true,
            ]
        );
        try {
            foreach ($extractor->extract() as $row) {
                static::fail('Since we asked more columns than available, an exception was expected');
            }
        } catch (InvalidInputException $exception) {
            static::assertEquals(
                'Asked columns quantity (4) is higher than the one really available (2)',
                $exception->getMessage()
            );
        }
    }

    /** @test */
    public function filteringColumnsWithIncompleteLine(): void
    {
        $extractor = new Csv();
        $extractor->input(__DIR__ . '/../data/incomplete_line.csv');

        // Without error handling (no BC).
        $extractor->options(
            [
                $extractor::COLUMNS => ['id', 'name', 'email'],
            ]
        );
        try {
            $count = 1;
            foreach ($extractor->extract() as $row) {
                $count++;
            }
        } catch (\Throwable $exception) {
            if (phpversion() < 8) {
                $expectedMessage = 'Undefined offset: 2';
            } else {
                $expectedMessage = 'Undefined array key 2';
            }

            static::assertEquals(
                $expectedMessage,
                $exception->getMessage()
            );
        }

        // With error handling
        $extractor->options(
            [
                $extractor::COLUMNS => ['id', 'name', 'email'],
                $extractor::THROW_ERROR => true,
            ]
        );
        try {
            $count = 1;
            foreach ($extractor->extract() as $row) {
                if (3 === $count) {
                    static::fail('Since we asked more columns than available, an exception was expected');
                }
                $count++;
            }
        } catch (InvalidInputException $exception) {
            static::assertEquals(
                'Row with index #4 only contains 2 elements while 3 were expected.',
                $exception->getMessage()
            );
        }
    }

    /** @test */
    public function filteringColumnsWithUnavailableField(): void
    {
        $extractor = new Csv();
        $extractor->input(__DIR__ . '/../data/incomplete_line.csv');

        // Without error handling (no BC).
        $extractor->options(
            [
                $extractor::COLUMNS => ['id', 'email'],
            ]
        );
        try {
            $count = 1;
            foreach ($extractor->extract() as $row) {
                $count++;
            }
        } catch (\Throwable $exception) {
            if (phpversion() < 8) {
                $expectedMessage = 'Undefined offset: 2';
            } else {
                $expectedMessage = 'Undefined array key 2';
            }

            static::assertEquals(
                $expectedMessage,
                $exception->getMessage()
            );
        }

        // With error handling
        $extractor->options(
            [
                $extractor::COLUMNS => ['id', 'email'],
                $extractor::THROW_ERROR => true,
            ]
        );
        try {
            $count = 1;
            foreach ($extractor->extract() as $row) {
                if (3 === $count) {
                    static::fail('Since we asked a field not available, an exception was expected');
                }
                $count++;
            }
        } catch (InvalidInputException $exception) {
            static::assertEquals(
                "Row with index #4 does not have the 'email' field.",
                $exception->getMessage()
            );
        }
    }

    /** @test */
    public function mappingColumns(): void
    {
        $expected = [
            new Row(['id' => 1, 'email_address' => 'johndoe@email.com']),
            new Row(['id' => 2, 'email_address' => 'janedoe@email.com']),
        ];

        $extractor = new Csv();

        $extractor->input(__DIR__ . '/../data/simple.csv');
        $extractor->options(
            [
                $extractor::COLUMNS => ['id' => 'id', 'email' => 'email_address'],
            ]
        );

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function customColumnsIndexesWhenThereIsNoHeader(): void
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Csv();

        $extractor->input(__DIR__ . '/../data/headless.csv');
        $extractor->options(
            [
                $extractor::COLUMNS => ['id' => 1, 'name' => 2, 'email' => 3],
            ]
        );

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    public function testMultiLineCsv(): void
    {
        $expected = [
            new Row(
                [
                    'id' => '1',
                    'name' => 'John

Doe',
                    'email' => 'johndoe@email.com',
                ]
            ),
            new Row(
                [
                    'id' => '2',
                    'name' => 'Jane Doe',
                    'email' => 'mail:
janedoe@email.com',
                ]
            ),
        ];

        $extractor = new Csv();
        $extractor->input(__DIR__ . '/../data/multiline.csv');

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    public function testMultiLineHeadCsv(): void
    {
        $expected = [
            new Row(
                [
                    'Column 1' => 'Value 1',
                    'Column 2' => 'Value 2',
                    "Multi-Line\nColumn 3" => 'Value 3',
                ]
            ),
        ];

        $extractor = new Csv();
        $extractor->input(__DIR__ . '/../data/multiline_head.csv');

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function missingFile(): void
    {
        $extractor = new Csv();
        $extractor->input(__DIR__ . '/../data/csv3trgrtg.csv');

        try {
            foreach ($extractor->extract() as $element) {
                static::fail('Since the file does not exist, an exception was expected');
            }
        } catch (IoException $exception) {
            static::assertEquals(
                "Impossible to open the file '" . __DIR__ . "/../data/csv3trgrtg.csv'",
                $exception->getMessage()
            );
        }
    }
}

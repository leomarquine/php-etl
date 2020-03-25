<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Tests\Extractors;

use Tests\TestCase;
use Wizaplace\Etl\Exception\InvalidInputException;
use Wizaplace\Etl\Exception\IoException;
use Wizaplace\Etl\Extractors\Csv;
use Wizaplace\Etl\Row;

class CsvTest extends TestCase
{
    /** @test */
    public function default_options()
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Csv();

        $extractor->input(__DIR__ . '/../data/csv1.csv');

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function custom_delimiter_and_enclosure()
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Csv();

        $extractor->input(__DIR__ . '/../data/csv2.csv');
        $extractor->options(['delimiter' => ';', 'enclosure' => '"']);

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function filtering_columns()
    {
        $expected = [
            new Row(['id' => 1, 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Csv();

        $extractor->input(__DIR__ . '/../data/csv1.csv');
        $extractor->options(['columns' => ['id', 'email']]);

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function filtering_columns_with_more_asked_columns_than_really_available()
    {
        $extractor = new Csv();
        $extractor->input(__DIR__ . '/../data/csv1.csv');

        // Without error handling (no BC). No message or error is expected.
        $extractor->options(['columns' => ['id', 'email', 'foo', 'bar']]);
        $data = [];
        foreach ($extractor->extract() as $row) {
            $data[] = $row;
        }

        // With error handling
        $extractor->options(['columns' => ['id', 'email', 'foo', 'bar'], 'throwError' => true]);

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
    public function filtering_columns_with_incomplete_line()
    {
        $extractor = new Csv();
        $extractor->input(__DIR__ . '/../data/incomplete_line.csv');

        // Without error handling (no BC).
        $extractor->options(['columns' => ['id', 'name', 'email']]);
        try {
            $count = 1;
            foreach ($extractor->extract() as $row) {
                if (3 === $count) {
                    static::fail('Since we asked more columns than available, an exception was expected');
                }
                $count++;
            }
        } catch (\Throwable $exception) {
            static::assertEquals(
                'Undefined offset: 2',
                $exception->getMessage()
            );
        }

        // With error handling
        $extractor->options(['columns' => ['id', 'name', 'email'], 'throwError' => true]);
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
    public function filtering_columns_with_unavailable_field()
    {
        $extractor = new Csv();
        $extractor->input(__DIR__ . '/../data/incomplete_line.csv');

        // Without error handling (no BC).
        $extractor->options(['columns' => ['id', 'email']]);
        try {
            $count = 1;
            foreach ($extractor->extract() as $row) {
                if (3 === $count) {
                    static::fail('Since we asked a field not available, an exception was expected');
                }
                $count++;
            }
        } catch (\Throwable $exception) {
            static::assertEquals(
                'Undefined offset: 2',
                $exception->getMessage()
            );
        }

        // With error handling
        $extractor->options(['columns' => ['id', 'email'], 'throwError' => true]);
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
    public function mapping_columns()
    {
        $expected = [
            new Row(['id' => 1, 'email_address' => 'johndoe@email.com']),
            new Row(['id' => 2, 'email_address' => 'janedoe@email.com']),
        ];

        $extractor = new Csv();

        $extractor->input(__DIR__ . '/../data/csv1.csv');
        $extractor->options(['columns' => ['id' => 'id', 'email' => 'email_address']]);

        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function custom_columns_indexes_when_there_is_no_header()
    {
        $expected = [
            new Row(['id' => 1, 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => 2, 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $extractor = new Csv();

        $extractor->input(__DIR__ . '/../data/csv3.csv');
        $extractor->options(['columns' => ['id' => 1, 'name' => 2, 'email' => 3]]);

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

    /** @test */
    public function missing_file(): void
    {
        $extractor = new Csv();
        $extractor->input(__DIR__ . '/../data/csv3trgrtg.csv');

        try {
            foreach ($extractor->extract() as $element) {
                static::fail('Since the file does not exist, an exception was expected');
            }
        } catch (IoException $exception) {
            static::assertEquals("Impossible to open the file '" . __DIR__ . "/../data/csv3trgrtg.csv'",
                $exception->getMessage()
            );
        }
    }
}

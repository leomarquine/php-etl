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
use Wizaplace\Etl\Extractors\DateDimension;
use Wizaplace\Etl\Row;

class DateDimensionTest extends TestCase
{
    private const DAY_AS_SECONDS = 24 * 60 * 60;

    /** @test */
    public function defaultOptions(): void
    {
        $expected = [
            new Row([
                DateDimension::ROW_DATE_KEY => '20200404',
                DateDimension::ROW_DATE_FULL_NAME => 'April 4, 2020',
                DateDimension::ROW_DATE_FULL => '2020-04-04T00:00:00+00:00',
                DateDimension::ROW_YEAR => '2020',
                DateDimension::ROW_QUARTER => 2,
                DateDimension::ROW_QUARTER_NAME => 'Q2',
                DateDimension::ROW_QUARTER_KEY => 2,
                DateDimension::ROW_MONTH => '4',
                DateDimension::ROW_MONTH_KEY => '4',
                DateDimension::ROW_MONTH_NAME => 'April',
                DateDimension::ROW_DAY_OF_MONTH => '4',
                DateDimension::ROW_NUMBER_OF_DAYS_IN_THE_MONTH => '30',
                DateDimension::ROW_DAY_OF_YEAR => 95,
                DateDimension::ROW_WEEK_OF_YEAR => '14',
                DateDimension::ROW_WEEK_OF_YEAR_KEY => '14',
                DateDimension::ROW_ISO_WEEK => '14',
                DateDimension::ROW_ISO_WEEK_KEY => '14',
                DateDimension::ROW_WEEK_DAY => '6',
                DateDimension::ROW_WEEK_DAY_NAME => 'Saturday',
                DateDimension::ROW_IS_WORK_DAY_KEY => 0,
            ]),
            new Row([
                DateDimension::ROW_DATE_KEY => '20200405',
                DateDimension::ROW_DATE_FULL_NAME => 'April 5, 2020',
                DateDimension::ROW_DATE_FULL => '2020-04-05T00:00:00+00:00',
                DateDimension::ROW_YEAR => '2020',
                DateDimension::ROW_QUARTER => 2,
                DateDimension::ROW_QUARTER_NAME => 'Q2',
                DateDimension::ROW_QUARTER_KEY => 2,
                DateDimension::ROW_MONTH => '4',
                DateDimension::ROW_MONTH_KEY => '4',
                DateDimension::ROW_MONTH_NAME => 'April',
                DateDimension::ROW_DAY_OF_MONTH => '5',
                DateDimension::ROW_NUMBER_OF_DAYS_IN_THE_MONTH => '30',
                DateDimension::ROW_DAY_OF_YEAR => 96,
                DateDimension::ROW_WEEK_OF_YEAR => '14',
                DateDimension::ROW_WEEK_OF_YEAR_KEY => '14',
                DateDimension::ROW_ISO_WEEK => '14',
                DateDimension::ROW_ISO_WEEK_KEY => '14',
                DateDimension::ROW_WEEK_DAY => '0',
                DateDimension::ROW_WEEK_DAY_NAME => 'Sunday',
                DateDimension::ROW_IS_WORK_DAY_KEY => 0,
            ]),
            new Row([
                DateDimension::ROW_DATE_KEY => '20200406',
                DateDimension::ROW_DATE_FULL_NAME => 'April 6, 2020',
                DateDimension::ROW_DATE_FULL => '2020-04-06T00:00:00+00:00',
                DateDimension::ROW_YEAR => '2020',
                DateDimension::ROW_QUARTER => 2,
                DateDimension::ROW_QUARTER_NAME => 'Q2',
                DateDimension::ROW_QUARTER_KEY => 2,
                DateDimension::ROW_MONTH => '4',
                DateDimension::ROW_MONTH_KEY => '4',
                DateDimension::ROW_MONTH_NAME => 'April',
                DateDimension::ROW_DAY_OF_MONTH => '6',
                DateDimension::ROW_NUMBER_OF_DAYS_IN_THE_MONTH => '30',
                DateDimension::ROW_DAY_OF_YEAR => 97,
                DateDimension::ROW_WEEK_OF_YEAR => '15',
                DateDimension::ROW_WEEK_OF_YEAR_KEY => '15',
                DateDimension::ROW_ISO_WEEK => '15',
                DateDimension::ROW_ISO_WEEK_KEY => '15',
                DateDimension::ROW_WEEK_DAY => '1',
                DateDimension::ROW_WEEK_DAY_NAME => 'Monday',
                DateDimension::ROW_IS_WORK_DAY_KEY => 1,
            ]),
        ];

        $extractor = new DateDimension();
        $extractor->options(
            [
                DateDimension::START_DATE => '2020-04-04T00:00:00+0',
                DateDimension::END_DATE => '2020-04-06T00:00:00+0',
            ]
        );
        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function selectedColumns(): void
    {
        $expected = [
            new Row([
                DateDimension::ROW_DATE_KEY => '20200101',
                DateDimension::ROW_DATE_FULL => '2020-01-01T06:00:00-04:00',
                DateDimension::ROW_YEAR => '2020',
                DateDimension::ROW_MONTH => '1',
                DateDimension::ROW_DAY_OF_MONTH => '1',
            ]),
            new Row([
                DateDimension::ROW_DATE_KEY => '20200102',
                DateDimension::ROW_DATE_FULL => '2020-01-02T06:00:00-04:00',
                DateDimension::ROW_YEAR => '2020',
                DateDimension::ROW_MONTH => '1',
                DateDimension::ROW_DAY_OF_MONTH => '2',
            ]),
            new Row([
                DateDimension::ROW_DATE_KEY => '20200103',
                DateDimension::ROW_DATE_FULL => '2020-01-03T06:00:00-04:00',
                DateDimension::ROW_YEAR => '2020',
                DateDimension::ROW_MONTH => '1',
                DateDimension::ROW_DAY_OF_MONTH => '3',
            ]),
        ];

        $extractor = new DateDimension();
        $extractor->options(
            [
                DateDimension::START_DATE => '2020-01-01T06:00:00-4',
                DateDimension::END_DATE => '2020-01-03T06:00:00-4',
                DateDimension::COLUMNS => [
                    DateDimension::ROW_DATE_KEY,
                    DateDimension::ROW_DATE_FULL,
                    DateDimension::ROW_YEAR,
                    DateDimension::ROW_MONTH,
                    DateDimension::ROW_DAY_OF_MONTH,
                ],
            ]
        );
        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function quarters(): void
    {
        $quarters = array_merge(
            array_fill(0, 31, 1),
            array_fill(0, 28, 1),
            array_fill(0, 31, 1),
            array_fill(0, 30, 2),
            array_fill(0, 31, 2),
            array_fill(0, 30, 2),
            array_fill(0, 31, 3),
            array_fill(0, 31, 3),
            array_fill(0, 30, 3),
            array_fill(0, 31, 4),
            array_fill(0, 30, 4),
            array_fill(0, 31, 4)
        );
        $expected = [];
        foreach ($quarters as $quarter) {
            $expected[] = new Row(
                [
                    DateDimension::ROW_QUARTER => $quarter,
                    DateDimension::ROW_QUARTER_NAME => "Q$quarter",
                ]
            );
        }

        $extractor = new DateDimension();
        $extractor->options(
            [
                DateDimension::START_DATE => '2021-01-01T06:00:00-4',
                DateDimension::END_DATE => '2021-12-31T06:00:00-4',
                DateDimension::COLUMNS => [
                    DateDimension::ROW_QUARTER,
                    DateDimension::ROW_QUARTER_NAME,
                ],
            ]
        );
        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function defaultStart(): void
    {
        date_default_timezone_set('America/New_York');

        $extractor = new DateDimension();
        $extractor->options(
            [
                DateDimension::COLUMNS => [
                    DateDimension::ROW_DATE_KEY,
                    DateDimension::ROW_DATE_FULL,
                ],
            ]
        );

        $firstDay = new \DateTimeImmutable('first day of January');
        $year = (int) $firstDay->format('Y');

        $result = \iterator_to_array($extractor->extract());
        $firstRow = reset($result[0]);
        $lastRow = end($result);

        static::assertStringMatchesFormat(
            '%d-%d-%dT00:00:00-%d:00',
            $firstRow[DateDimension::ROW_DATE_FULL]
        );
        static::assertStringMatchesFormat(
            '%d-%d-%dT00:00:00-%d:00',
            $lastRow[DateDimension::ROW_DATE_FULL]
        );
        static::assertStringContainsString('12-31', $lastRow[DateDimension::ROW_DATE_FULL]);
        static::assertGreaterThan(3650, count($result));
        static::assertEquals($year - 5 . '0101', $firstRow[DateDimension::ROW_DATE_KEY]);
        static::assertEquals($year + 4 . '1231', $lastRow[DateDimension::ROW_DATE_KEY]);
    }

    /** @test */
    public function handlesDaylightSavingsTenYears(): void
    {
        date_default_timezone_set('America/New_York');

        $extractor = new DateDimension();
        $extractor->options(
            [
                DateDimension::START_DATE => '2016-01-01',
                DateDimension::END_DATE => '2025-12-31',
                DateDimension::COLUMNS => [
                    DateDimension::ROW_DATE_KEY,
                    DateDimension::ROW_DATE_FULL,
                ],
            ]
        );

        [$commonDays, $longDays, $shortDays, $gainedTime] = $this->iterateDimensions($extractor);
        self::assertEquals(3633, $commonDays);
        self::assertEquals(10, $longDays);
        self::assertEquals(10, $shortDays);
        self::assertEquals(0, $gainedTime);
    }

    /** @test */
    public function handlesDaylightSavingsAYearAndAHalf(): void
    {
        date_default_timezone_set('America/New_York');

        $extractor = new DateDimension();
        $extractor->options(
            [
                DateDimension::START_DATE => '2019-01-01',
                DateDimension::END_DATE => '2020-07-01',
                DateDimension::COLUMNS => [
                    DateDimension::ROW_DATE_KEY,
                    DateDimension::ROW_DATE_FULL,
                ],
            ]
        );

        [$commonDays, $longDays, $shortDays, $gainedTime] = $this->iterateDimensions($extractor);
        self::assertEquals(545, $commonDays);
        self::assertEquals(1, $longDays);
        self::assertEquals(2, $shortDays);
        self::assertEquals(-3600, $gainedTime);
    }

    /** @test */
    public function handlesDaylightSavingsDefaultStart(): void
    {
        date_default_timezone_set('America/New_York');

        $extractor = new DateDimension();
        $extractor->options(
            [
                DateDimension::COLUMNS => [
                    DateDimension::ROW_DATE_KEY,
                    DateDimension::ROW_DATE_FULL,
                ],
            ]
        );

        [$commonDays, $longDays, $shortDays, $gainedTime] = $this->iterateDimensions($extractor);

        self::assertGreaterThan(3630, $commonDays);
        self::assertEquals(10, $longDays);
        self::assertEquals(10, $shortDays);
        self::assertEquals(0, $gainedTime);
    }

    /** @test */
    public function handlesDaylightSavingsUtc(): void
    {
        date_default_timezone_set('UTC');

        $extractor = new DateDimension();
        $extractor->options(
            [
                DateDimension::START_DATE => '2022-01-01',
                DateDimension::END_DATE => '2022-12-31',
                DateDimension::COLUMNS => [
                    DateDimension::ROW_DATE_KEY,
                    DateDimension::ROW_DATE_FULL,
                ],
            ]
        );

        [$commonDays, $longDays, $shortDays, $gainedTime] = $this->iterateDimensions($extractor);

        self::assertEquals(365, $commonDays);
        self::assertEquals(0, $longDays);
        self::assertEquals(0, $shortDays);
        self::assertEquals(0, $gainedTime);
    }

    private function iterateDimensions(DateDimension $extractor): array
    {
        $commonDays = 0;
        $shortDays = 0;
        $longDays = 0;
        $gainedTime = 0;

        $previousDayTimestamp = null;
        $delta = 0;

        foreach ($extractor->extract() as $date) {
            $currentDayTimestamp = (new \DateTimeImmutable($date[DateDimension::ROW_DATE_FULL]))->getTimestamp();

            if (null !== $previousDayTimestamp) {
                $delta = $currentDayTimestamp - $previousDayTimestamp - static::DAY_AS_SECONDS;
            }

            if ($delta > 0) {
                $longDays++;
                $gainedTime += $delta;
            } elseif ($delta < 0) {
                $shortDays++;
                $gainedTime += $delta;
            } else {
                $commonDays++;
            }

            $previousDayTimestamp = $currentDayTimestamp;
        }

        return [
            $commonDays,
            $longDays,
            $shortDays,
            $gainedTime,
        ];
    }
}

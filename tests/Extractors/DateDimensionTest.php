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
    /** @test */
    public function defaultOptions(): void
    {
        $expected = [
            new Row([
                'DateKey' => '20200404',
                'DateFullName' => 'April 4, 2020',
                'DateFull' => '2020-04-04T00:00:00+00:00',
                'Year' => '2020',
                'Quarter' => 2,
                'QuarterName' => 'Q2',
                'QuarterKey' => 2,
                'Month' => '4',
                'MonthKey' => '4',
                'MonthName' => 'April',
                'DayOfMonth' => '4',
                'NumberOfDaysInTheMonth' => '30',
                'DayOfYear' => 95,
                'WeekOfYear' => '14',
                'WeekOfYearKey' => '14',
                'ISOWeek' => '14',
                'ISOWeekKey' => '14',
                'WeekDay' => '6',
                'WeekDayName' => 'Saturday',
                'IsWorkDayKey' => 0,
            ]),
            new Row([
                'DateKey' => '20200405',
                'DateFullName' => 'April 5, 2020',
                'DateFull' => '2020-04-05T00:00:00+00:00',
                'Year' => '2020',
                'Quarter' => 2,
                'QuarterName' => 'Q2',
                'QuarterKey' => 2,
                'Month' => '4',
                'MonthKey' => '4',
                'MonthName' => 'April',
                'DayOfMonth' => '5',
                'NumberOfDaysInTheMonth' => '30',
                'DayOfYear' => 96,
                'WeekOfYear' => '14',
                'WeekOfYearKey' => '14',
                'ISOWeek' => '14',
                'ISOWeekKey' => '14',
                'WeekDay' => '0',
                'WeekDayName' => 'Sunday',
                'IsWorkDayKey' => 0,
            ]),
            new Row([
                'DateKey' => '20200406',
                'DateFullName' => 'April 6, 2020',
                'DateFull' => '2020-04-06T00:00:00+00:00',
                'Year' => '2020',
                'Quarter' => 2,
                'QuarterName' => 'Q2',
                'QuarterKey' => 2,
                'Month' => '4',
                'MonthKey' => '4',
                'MonthName' => 'April',
                'DayOfMonth' => '6',
                'NumberOfDaysInTheMonth' => '30',
                'DayOfYear' => 97,
                'WeekOfYear' => '15',
                'WeekOfYearKey' => '15',
                'ISOWeek' => '15',
                'ISOWeekKey' => '15',
                'WeekDay' => '1',
                'WeekDayName' => 'Monday',
                'IsWorkDayKey' => 1,
            ]),
        ];

        $extractor = new DateDimension();
        $extractor->options(['startDate' => '2020-04-04T00:00:00+0', 'endDate' => '2020-04-06T00:00:00+0']);
        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function selectedColumns(): void
    {
        $expected = [
            new Row([
                'DateKey' => '20200101',
                'DateFull' => '2020-01-01T06:00:00-04:00',
                'Year' => '2020',
                'Month' => '1',
                'DayOfMonth' => '1',
            ]),
            new Row([
                'DateKey' => '20200102',
                'DateFull' => '2020-01-02T06:00:00-04:00',
                'Year' => '2020',
                'Month' => '1',
                'DayOfMonth' => '2',
            ]),
            new Row([
                'DateKey' => '20200103',
                'DateFull' => '2020-01-03T06:00:00-04:00',
                'Year' => '2020',
                'Month' => '1',
                'DayOfMonth' => '3',
            ]),
        ];

        $extractor = new DateDimension();
        $extractor->options([
            'startDate' => '2020-01-01T06:00:00-4',
            'endDate' => '2020-01-03T06:00:00-4',
            'columns' => ['DateKey', 'DateFull', 'Year', 'Month', 'DayOfMonth'],
        ]);
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
            $expected[] = new Row(['Quarter' => $quarter, 'QuarterName' => "Q$quarter"]);
        }

        $extractor = new DateDimension();
        $extractor->options([
            'startDate' => '2021-01-01T06:00:00-4',
            'endDate' => '2021-12-31T06:00:00-4',
            'columns' => ['Quarter', 'QuarterName'],
        ]);
        static::assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function defaultStart(): void
    {
        ini_set('date.timezone', 'America/New_York');
        $year = (int) (new \DateTime())->format('Y');
        $offset = (new \DateTime())->format('P');

        $extractor = new DateDimension();
        $extractor->options([
            'columns' => ['DateKey', 'DateFull'],
        ]);
        $result = iterator_to_array($extractor->extract());

        static::assertStringEndsWith("00:00:00$offset", $result[0]['DateFull']);
        static::assertStringEndsWith("00:00:00$offset", $result[count($result) - 1]['DateFull']);
        static::assertStringContainsString('12-31', $result[count($result) - 1]['DateFull']);
        static::assertGreaterThan(3650, count($result));
        static::assertEquals($year - 5 . '0101', $result[0]['DateKey']);
        static::assertEquals($year + 4 . '1231', $result[count($result) - 1]['DateKey']);
    }
}

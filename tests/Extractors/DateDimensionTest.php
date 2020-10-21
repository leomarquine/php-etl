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
    public function default_options()
    {
        $expected = [
            new Row([
                'DateKey' => '20200101',
                'DateFullName' => 'January 1, 2020',
                'DateFull' => '2020-01-01T00:00:00+00:00',
                'Year' => '2020',
                'Quarter' => 1,
                'QuarterName' => 'Q1',
                'QuarterKey' => 1,
                'Month' => '1',
                'MonthKey' => '1',
                'MonthName' => 'January',
                'DayOfMonth' => '1',
                'NumberOfDaysInTheMonth' => '31',
                'DayOfYear' => 1,
                'WeekOfYear' => '01',
                'WeekOfYearKey' => '01',
                'ISOWeek' => '01',
                'ISOWeekKey' => '01',
                'WeekDay' => '3',
                'WeekDayName' => 'Wednesday',
                'IsWorkDayKey' => 1,
            ]),
            new Row([
                'DateKey' => '20200102',
                'DateFullName' => 'January 2, 2020',
                'DateFull' => '2020-01-02T00:00:00+00:00',
                'Year' => '2020',
                'Quarter' => 1,
                'QuarterName' => 'Q1',
                'QuarterKey' => 1,
                'Month' => '1',
                'MonthKey' => '1',
                'MonthName' => 'January',
                'DayOfMonth' => '2',
                'NumberOfDaysInTheMonth' => '31',
                'DayOfYear' => 2,
                'WeekOfYear' => '01',
                'WeekOfYearKey' => '01',
                'ISOWeek' => '01',
                'ISOWeekKey' => '01',
                'WeekDay' => '4',
                'WeekDayName' => 'Thursday',
                'IsWorkDayKey' => 1,
            ]),
        ];

        $extractor = new DateDimension();
        $extractor->options(['startDate' => '2020-01-01T00:00:00+0', 'endDate' => '2020-01-02T00:00:00+0']);
        $this->assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function selected_columns()
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
        $this->assertEquals($expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function default_start()
    {
        $extractor = new DateDimension();
        $extractor->options([
            'columns' => ['DateKey', 'DateFull'],
        ]);
        $result = iterator_to_array($extractor->extract());
        $this->assertGreaterThan(3650, count($result));
        $year = (int) (new \DateTime())->format('Y');
        $this->assertEquals($year - 5 . '0101', $result[0]['DateKey']);
        $this->assertEquals($year + 5 . '0101', $result[count($result) - 1]['DateKey']);
    }
}

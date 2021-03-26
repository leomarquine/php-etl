<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Extractors;

use Wizaplace\Etl\Row;

/**
 * Provides a tool to pre-generate a date dimension table.
 */
class DateDimension extends Extractor
{
    public const END_DATE = 'endDate';
    public const START_DATE = 'startDate';

    /** row keys */
    public const ROW_DATE_KEY = 'DateKey';
    public const ROW_DATE_FULL_NAME = 'DateFullName';
    public const ROW_DATE_FULL = 'DateFull';
    public const ROW_YEAR = 'Year';
    public const ROW_QUARTER = 'Quarter';
    public const ROW_QUARTER_NAME = 'QuarterName';
    public const ROW_QUARTER_KEY = 'QuarterKey';
    public const ROW_MONTH = 'Month';
    public const ROW_MONTH_KEY = 'MonthKey';
    public const ROW_MONTH_NAME = 'MonthName';
    public const ROW_DAY_OF_MONTH = 'DayOfMonth';
    public const ROW_NUMBER_OF_DAYS_IN_THE_MONTH = 'NumberOfDaysInTheMonth';
    public const ROW_DAY_OF_YEAR = 'DayOfYear';
    public const ROW_WEEK_OF_YEAR = 'WeekOfYear';
    public const ROW_WEEK_OF_YEAR_KEY = 'WeekOfYearKey';
    public const ROW_ISO_WEEK = 'ISOWeek';
    public const ROW_ISO_WEEK_KEY = 'ISOWeekKey';
    public const ROW_WEEK_DAY = 'WeekDay';
    public const ROW_WEEK_DAY_NAME = 'WeekDayName';
    public const ROW_IS_WORK_DAY_KEY = 'IsWorkDayKey';

    private const CENTER_DATE_FORMAT = 'Y-m-d';

    /**
     * A string representing the start date of the requested dimension table.
     */
    protected string $startDate;

    /**
     * A string representing the end date of the requested dimension table.
     */
    protected string $endDate;

    protected array $columns = [];

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected array $availableOptions = [
        self::COLUMNS,
        self::START_DATE,
        self::END_DATE,
    ];

    public function __construct()
    {
        $defaultBoundInterval = new \DateInterval('P5Y');
        $dayInterval = new \DateInterval('P1D');

        $this->startDate ??= $this->getCenterDateTime()
            ->sub($defaultBoundInterval)
            ->format(static::CENTER_DATE_FORMAT);

        $this->endDate ??= $this->getCenterDateTime()
            ->add($defaultBoundInterval)
            ->sub($dayInterval)
            ->format(static::CENTER_DATE_FORMAT);
    }

    /**
     * Extract data from the input.
     */
    public function extract(): \Generator
    {
        foreach ($this->getDatePeriod() as $date) {
            $dayOfWeek = (int) $date->format('w');
            $quarter = (int) ceil($date->format('n') / 3);

            $row = [
                static::ROW_DATE_KEY => $date->format('Ymd'),
                static::ROW_DATE_FULL_NAME => $date->format('F j, Y'),
                static::ROW_DATE_FULL => $date->format('c'),
                static::ROW_YEAR => $date->format('Y'),
                static::ROW_QUARTER => $quarter,
                static::ROW_QUARTER_NAME => "Q$quarter",
                static::ROW_QUARTER_KEY => $quarter,
                static::ROW_MONTH => $date->format('n'),
                static::ROW_MONTH_KEY => $date->format('n'),
                static::ROW_MONTH_NAME => $date->format('F'),
                static::ROW_DAY_OF_MONTH => $date->format('j'),
                static::ROW_NUMBER_OF_DAYS_IN_THE_MONTH => $date->format('t'),
                static::ROW_DAY_OF_YEAR => 1 + (int) $date->format('z'),
                static::ROW_WEEK_OF_YEAR => $date->format('W'),
                static::ROW_WEEK_OF_YEAR_KEY => $date->format('W'),
                static::ROW_ISO_WEEK => $date->format('W'),
                static::ROW_ISO_WEEK_KEY => $date->format('W'),
                static::ROW_WEEK_DAY => $dayOfWeek,
                static::ROW_WEEK_DAY_NAME => $date->format('l'),
                static::ROW_IS_WORK_DAY_KEY => (0 === $dayOfWeek || 6 === $dayOfWeek) ? 0 : 1,
            ];

            if ([] !== $this->columns) {
                $flipped = array_flip($this->columns);
                $row = array_intersect_key($row, $flipped);
            }

            yield new Row($row);
        }
    }

    private function getCenterDateTime(): \DateTimeImmutable
    {
        return new \DateTimeImmutable(
            'first day of January',
        );
    }

    private function getDatePeriod(): \DatePeriod
    {
        $dayInterval = new \DateInterval('P1D');

        return new \DatePeriod(
            new \DateTime($this->startDate),
            $dayInterval,
            (new \DateTime($this->endDate))
                ->add($dayInterval)
        );
    }
}

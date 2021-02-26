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
    protected array $availableOptions = ['columns', 'startDate', 'endDate'];

    public function __construct()
    {
        if (!isset($this->startDate)) {
            $start = new \DateTime();
            $start->sub(new \DateInterval('P5Y'))
                ->setDate((int) $start->format('Y'), 1, 1)
                ->setTime(0, 0);
            $this->startDate = $start->format('c');
        }
        if (!isset($this->endDate)) {
            $end = new \DateTime();
            $end->add(new \DateInterval('P5Y'))
                ->setDate((int) $end->format('Y'), 1, 1)
                ->sub(new \DateInterval('P1D'));
            $this->endDate = $end->format('c');
        }
    }

    /**
     * Extract data from the input.
     */
    public function extract(): \Generator
    {
        $interval = new \DateInterval('P1D');
        $date = new \DateTime($this->startDate);
        $end = new \DateTime($this->endDate);
        while ($date <= $end) {
            $dayOfWeek = (int) $date->format('w');
            $quarter = (int) ceil($date->format('n') / 3);
            $row = [
                'DateKey' => $date->format('Ymd'),
                'DateFullName' => $date->format('F j, Y'),
                'DateFull' => $date->format('c'),
                'Year' => $date->format('Y'),
                'Quarter' => $quarter,
                'QuarterName' => "Q$quarter",
                'QuarterKey' => $quarter,
                'Month' => $date->format('n'),
                'MonthKey' => $date->format('n'),
                'MonthName' => $date->format('F'),
                'DayOfMonth' => $date->format('j'),
                'NumberOfDaysInTheMonth' => $date->format('t'),
                'DayOfYear' => 1 + (int) $date->format('z'),
                'WeekOfYear' => $date->format('W'),
                'WeekOfYearKey' => $date->format('W'),
                'ISOWeek' => $date->format('W'),
                'ISOWeekKey' => $date->format('W'),
                'WeekDay' => $dayOfWeek,
                'WeekDayName' => $date->format('l'),
                'IsWorkDayKey' => (0 === $dayOfWeek || 6 === $dayOfWeek) ? 0 : 1,
            ];

            if ([] !== $this->columns) {
                $flipped = array_flip($this->columns);
                $row = array_intersect_key($row, $flipped);
            }

            yield new Row($row);

            // Add one day to set up the next iteration of the loop.
            $date->add($interval);
        }
    }
}

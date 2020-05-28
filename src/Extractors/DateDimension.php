<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Row;

class DateDimension extends Extractor
{
    protected $startDate;

    protected $endDate;

    /**
     * Extractor columns.
     *
     * @var array
     */
    protected $columns;

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns', 'startDate', 'endDate'
    ];

    public function __construct() {
        if (empty($this->startDate)) {
            $date = new \DateTime();
            $date->sub(new \DateInterval('P5Y'))
                ->setDate($date->format('Y'), 1, 1)
                ->setTime(0, 0, 0);
            $this->startDate = $date->format('c');
        }
        if (empty($this->endDate)) {
            $date = new \DateTime();
            $date->add(new \DateInterval('P5Y'))
                ->setDate($date->format('Y'), 1, 1)
                ->setTime(0, 0, 0);
            $this->endDate = $date->format('c');
        }
    }

    /**
     * Extract data from the input.
     *
     * @return \Generator
     */
    public function extract()
    {
        if ($this->columns) {
            $flipped = array_flip($this->columns);
        }

        $interval = new \DateInterval('P1D');
        $date = new \DateTime($this->startDate);
        $end = new \DateTime($this->endDate);
        while ($date <= $end) {
            $dayOfWeek = $date->format('w');
            $row = [
                'DateKey' => $date->format('Ymd'),
                'DateFullName' => $date->format('F j, Y'),
                'DateFull' => $date->format('c'),
                'Year' => $date->format('Y'),
                'Quarter' => (int) ceil($date->format('n') / 4),
                'QuarterName' => 'Q' . (int) ceil($date->format('n') / 4),
                'QuarterKey' => (int) ceil($date->format('n') / 4),
                'Month' => $date->format('n'),
                'MonthKey' => $date->format('n'),
                'MonthName' => $date->format('F'),
                'DayOfMonth' => $date->format('j'),
                'NumberOfDaysInTheMonth' => $date->format('t'),
                'DayOfYear' => 1 + $date->format('z'),
                'WeekOfYear' => $date->format('W'),
                'WeekOfYearKey' => $date->format('W'),
                'ISOWeek' => $date->format('W'),
                'ISOWeekKey' => $date->format('W'),
                'WeekDay' => $dayOfWeek,
                'WeekDayName' => $date->format('l'),
                'IsWorkDayKey' => ($dayOfWeek === 0 || $dayOfWeek === 6) ? 0 : 1,
            ];
            $date->add($interval);

            if ($this->columns) {
                $row = array_intersect_key($row, $flipped);
            }

            yield new Row($row);
        }
    }
}

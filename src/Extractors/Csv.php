<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Wizaplace\Etl\Extractors;

use Wizaplace\Etl\Row;

class Csv extends Extractor
{
    /**
     * Extractor columns.
     *
     * @var array
     */
    protected $columns;

    /**
     * The delimiter string.
     *
     * @var string
     */
    protected $delimiter = ',';

    /**
     * The enclosure string.
     *
     * @var string
     */
    protected $enclosure = '';

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns', 'delimiter', 'enclosure',
    ];

    /**
     * Extract data from the input.
     */
    public function extract(): \Generator
    {
        $handle = fopen($this->input, 'r');

        $columns = $this->makeColumns($handle);

        while ($row = fgets($handle)) {
            yield new Row($this->makeRow($row, $columns));
        }

        fclose($handle);
    }

    /**
     * Converts the row string to array.
     */
    protected function makeRow(string $row, array $columns): array
    {
        $row = str_getcsv($row, $this->delimiter, $this->enclosure);

        $data = [];

        foreach ($columns as $column => $index) {
            $data[$column] = $row[$index - 1];
        }

        return $data;
    }

    /**
     * Make columns based on csv header.
     *
     * @param resource $handle
     */
    protected function makeColumns($handle): array
    {
        if (is_array($this->columns) && is_numeric(current($this->columns))) {
            return $this->columns;
        }

        $columns = array_flip(str_getcsv(fgets($handle), $this->delimiter, $this->enclosure));

        foreach ($columns as $key => $index) {
            $columns[$key] = $index + 1;
        }

        if (empty($this->columns)) {
            return $columns;
        }

        if (array_keys($this->columns) === range(0, count($this->columns) - 1)) {
            return array_intersect_key($columns, array_flip($this->columns));
        }

        $result = [];

        foreach ($this->columns as $key => $value) {
            $result[$value] = $columns[$key];
        }

        return $result;
    }
}

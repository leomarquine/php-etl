<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Wizaplace\Etl\Extractors;

use Wizaplace\Etl\Exception\InvalidInputException;
use Wizaplace\Etl\Exception\IoException;
use Wizaplace\Etl\Row;

class Csv extends Extractor
{
    /**
     * Extractor columns.
     *
     * @var array|null
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
    protected $enclosure = '"';

    /**
     * Throw error if invalid data. Set to false to keep backward compatibility with older versions.
     *
     * @var bool
     */
    protected $throwError = false;

    /**
     * The 'currentRow' attribute is global because used in many places and we don't want to change the
     * signature of the methods
     *
     * @var int |null
     */
    protected $currentRow;

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns', 'delimiter', 'enclosure', 'throwError',
    ];

    /** Note that this method could be removed when dropping PHP < 7.4 support, using attribute types. */
    public function initialize(): void
    {
        $this->currentRow = null;

        if (false === is_bool($this->throwError)) {
            $this->throwError = false;
        }
    }

    /**
     * Extract data from the input.
     */
    public function extract(): \Generator
    {
        $handle = @fopen($this->input, 'r');
        if (false === $handle) {
            throw new IoException("Impossible to open the file '{$this->input}'");
        }

        $columns = $this->makeColumns($handle);
        $this->currentRow = 1;

        $this->validateFilteredColumns(count($columns));

        while ($row = fgetcsv($handle, 0, $this->delimiter, $this->enclosure)) {
            $this->currentRow++;

            yield new Row($this->makeRow($row, $columns));
        }

        fclose($handle);
    }

    /**
     * Converts the row string to array.
     *
     * @param string[] $row
     * @param int[]    $columns
     */
    protected function makeRow(array $row, array $columns): array
    {
        $data = [];

        $rowColumnsCount = count($row);
        $columnsCount = is_array($this->columns) ? count($this->columns) : count($columns);

        if (true === $this->throwError && $rowColumnsCount < $columnsCount) {
            $message = "Row with index #{$this->currentRow} only contains $rowColumnsCount "
                . "elements while $columnsCount were expected.";
            throw new InvalidInputException($message);
        }

        foreach ($columns as $column => $index) {
            if ($this->throwError && false === array_key_exists($index - 1, $row)) {
                throw new InvalidInputException("Row with index #{$this->currentRow} does not have the '{$column}' field.");
            }
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

    protected function validateFilteredColumns(int $columnsCount): void
    {
        if (true === is_array($this->columns) && [] !== $this->columns) {
            $askedColumnsCount = count($this->columns);

            if (true === $this->throwError && $askedColumnsCount > $columnsCount) {
                $message = "Asked columns quantity ($askedColumnsCount) is higher than the one really "
                    . "available ($columnsCount)";
                throw new InvalidInputException($message);
            }
        }
    }
}

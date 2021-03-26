<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Extractors;

use Wizaplace\Etl\Exception\InvalidInputException;
use Wizaplace\Etl\Exception\IoException;
use Wizaplace\Etl\Row;

class Csv extends Extractor
{
    public const DELIMITER = 'delimiter';
    public const ENCLOSURE = 'enclosure';
    public const THROW_ERROR = 'throwError';

    protected ?array $columns = null;

    /**
     * The delimiter string.
     */
    protected string $delimiter = ',';

    /**
     * The enclosure string.
     */
    protected string $enclosure = '"';

    /**
     * Throw error if invalid data. Set to false to keep backward compatibility with older versions.
     */
    protected bool $throwError = false;

    /**
     * The 'currentRow' attribute is global because used in many places and we don't want to change the
     * signature of the methods.
     */
    protected int $currentRow = 0;

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected array $availableOptions = [
        self::COLUMNS,
        self::DELIMITER,
        self::ENCLOSURE,
        self::THROW_ERROR,
    ];

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
     *
     * @throws InvalidInputException
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
            // The is bool is redundant, it is necessary because of PHP Stan, since we check the type in initialize()
            if ($this->throwError && false === array_key_exists($index - 1, $row)) {
                $message = "Row with index #{$this->currentRow} does not have the '{$column}' field.";
                throw new InvalidInputException($message);
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

        $columns = array_flip(
            fgetcsv($handle, 0, $this->delimiter, $this->enclosure)
        );

        foreach ($columns as $key => $index) {
            $columns[$key] = $index + 1;
        }

        if (false === is_array($this->columns) || [] === $this->columns) {
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

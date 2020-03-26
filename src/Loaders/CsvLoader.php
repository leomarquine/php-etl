<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

namespace Wizaplace\Etl\Loaders;

use Wizaplace\Etl\Database\Manager;
use Wizaplace\Etl\Exception\IoException;
use Wizaplace\Etl\Row;

class CsvLoader extends Loader
{
    /**
     * Count how many lines have been loaded
     * @var int
     */
    protected $loaderCounter = 0;

    /**
     * Count how many files have been created
     * @var int
     */
    protected $fileCounter = 0;

    /**
     * CSV file handler
     * @var resource|bool
     */
    protected $fileHandler;

    /**
     * All available options for this loader
     * @var string[]
     */
    protected $availableOptions = [
        'delimiter', 'enclosure', 'escapeChar', 'linePerFile'
    ];

    /**
     * The CSV delimiter string.
     * @var string
     */
    protected $delimiter = ';';

    /**
     * The CSV enclosure string.
     * @var string
     */
    protected $enclosure = '"';

    /**
     * The CSV escaping string.
     * @var string
     */
    protected $escapeChar = '\\';

    /**
     * Max lines per file
     * @var int
     */
    protected $linePerFile = -1;

    public function initialize(): void
    {
        $fileUri = $this->output . '_' . $this->fileCounter . '.csv';
        $this->fileHandler = @fopen($fileUri, 'w+');

        if (false === $this->fileHandler) {
            throw new IoException("Impossible to open the file '{$fileUri}'");
        }
    }

    /**
     * Finalize the step.
     */
    public function finalize(): void
    {
        fclose($this->fileHandler);
    }

    /**
     * Load the given row.
     */
    public function load(Row $row): void
    {
        // If we reach the max lines, we open a new file
        if (-1 !== $this->linePerFile && $this->loaderCounter >= $this->linePerFile) {
            $this->loaderCounter = 0;
            $this->fileCounter++;

            $this->finalize();
            $this->initialize();
        }

        $rowArray = $row->toArray();

        if (0 === $this->loaderCounter) {
            $this->putCsv($this->getHeaders($rowArray));
        }

        $this->putCsv($rowArray);
        $this->loaderCounter++;
    }

    /**
     * CSV headers generation
     *
     * @param array[]|string[] $rowArray
     *
     * @return string[]
     */
    protected function getHeaders(array $rowArray): array
    {
        $headers = [];

        foreach ($rowArray as $columnName => $rowColumn) {
            $headers[] = $columnName;
        }

        return $headers;
    }

    /**
     * Insert data into CSV file
     *
     * @param string[] $data
     */
    protected function putCsv(array $data): void
    {
        fputcsv
        (
            $this->fileHandler,
            $data,
            $this->delimiter,
            $this->enclosure,
            $this->escapeChar
        );
    }
}

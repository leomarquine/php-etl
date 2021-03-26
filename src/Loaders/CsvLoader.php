<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Loaders;

use Wizaplace\Etl\Exception\IoException;
use Wizaplace\Etl\Row;
use Wizaplace\Etl\Traits\FilePathTrait;

class CsvLoader extends Loader
{
    use FilePathTrait;

    public const DELIMITER = 'delimiter';
    public const ENCLOSURE = 'enclosure';
    public const ESCAPE_CHAR = 'escapeChar';
    public const LINE_PER_FILE = 'linePerFile';

    /**
     * Count how many lines have been loaded
     */
    protected int $loaderCounter = 0;

    /**
     * Count how many files have been created
     */
    protected int $fileCounter = 1;

    /**
     * CSV file handler
     *
     * @var resource|bool
     */
    protected $fileHandler;

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected array $availableOptions = [
        self::DELIMITER,
        self::ENCLOSURE,
        self::ESCAPE_CHAR,
        self::LINE_PER_FILE,
    ];

    /**
     * The CSV delimiter string.
     */
    protected string $delimiter = ';';

    /**
     * The CSV enclosure string.
     */
    protected string $enclosure = '"';

    /**
     * The CSV escaping string.
     */
    protected string $escapeChar = '\\';

    /**
     * Max lines per file
     */
    protected int $linePerFile = -1;

    public function initialize(): void
    {
        $this->openFile();
    }

    /**
     * Finalize the step.
     */
    public function finalize(): void
    {
        $this->closeFile();
    }

    /**
     * Load the given row.
     */
    public function load(Row $row): void
    {
        // If we reach the max lines, we open a new file
        if (
            0 < $this->linePerFile
            && $this->linePerFile <= $this->loaderCounter
        ) {
            $this->loaderCounter = 0;
            $this->fileCounter++;

            $this->closeFile();
            $this->openFile();
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
        return \array_keys($rowArray);
    }

    protected function openFile(): void
    {
        $fileUri = $this->getFileUri(
            $this->output,
            $this->linePerFile,
            $this->fileCounter
        );
        $this->checkOrCreateDir($fileUri);
        $this->fileHandler = @\fopen($fileUri, 'w+');

        if (false === $this->fileHandler) {
            throw new IoException("Impossible to open the file '{$fileUri}'");
        }
    }

    protected function closeFile(): void
    {
        \fclose($this->fileHandler);
    }

    /**
     * Insert data into CSV file
     *
     * @param string[] $data
     */
    protected function putCsv(array $data): void
    {
        \fputcsv(
            $this->fileHandler,
            $data,
            $this->delimiter,
            $this->enclosure,
            $this->escapeChar
        );
    }
}

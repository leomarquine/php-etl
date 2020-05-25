<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

namespace Wizaplace\Etl\Extractors;

use Wizaplace\Etl\DirtyRow;
use Wizaplace\Etl\Exception\IncompleteDataException;
use Wizaplace\Etl\Exception\InvalidOptionException;
use Wizaplace\Etl\Exception\UndefinedIndexException;
use Wizaplace\Etl\Row;

class Aggregator extends Extractor
{
    /**
     * The matching key tuplet between iterators.
     *
     * @var string[]
     */
    protected $index;

    /**
     * Columns.
     *
     * @var string[]
     */
    protected $columns;

    /**
     * If set to true,
     * will throw a MissingDataException if there is any incomplete rows remaining
     * when all input iterators are fully consumed and closed.
     *
     * @var bool
     */
    protected $strict = true;

    /** @var array[] */
    protected $data;

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'index',
        'columns',
        'strict',
    ];

    /**
     * Properties that MUST be set via the options method.
     *
     * @var array
     */
    protected $requiredOptions = [
        'index',
        'columns',
    ];

    /**
     * @return \Generator<Row>
     *
     * @throws IncompleteDataException
     */
    public function extract(): \Generator
    {
        // consume input iterators
        do {
            foreach ($this->input as $iterator) {
                if (
                    ($line = $iterator->current())
                    && ($row = $this->build($line))
                ) {
                    yield new Row($row);
                }
                $iterator->next();
            }
        } while (
            $this->hasValidInput()
        );

        $incompletes = \count($this->data);
        if ($this->strict && $incompletes) {
            throw new IncompleteDataException("$incompletes rows");
        }

        // then yield the incomplete remaining rows
        foreach ($this->data as $row) {
            yield (new Row($row))->setIncomplete();
        }
    }

    /**
     * Accumulate row data and return when completed.
     *
     * @param mixed[] $line
     *
     * @return mixed[]
     */
    protected function build(array $line): ?array
    {
        try {
            $hash = $this->lineHash($line);
        } catch (UndefinedIndexException $exception) {
            return null;
        }

        $this->data[$hash] = \array_merge(
            $this->data[$hash] ?? [],
            $line
        );

        if ($this->isCompleted($hash)) {
            $row = $this->data[$hash];
            unset($this->data[$hash]); // free the RAM

            return $row;
        }

        return null;
    }

    /**
     * Check if row is completed.
     */
    protected function isCompleted(string $hash): bool
    {
        if (!\is_array($this->columns)) {
            throw new InvalidOptionException('invalid columns', 2);
        }

        foreach ($this->columns as $key) {
            if (!\array_key_exists($key, $this->data[$hash])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if there is any opened iterators left.
     */
    protected function hasValidInput(): bool
    {
        foreach ($this->input as $iterator) {
            if ($iterator->valid()) {
                return true;
            }
        }

        return false;
    }

    /**
     * calculate row hash key from specified index array.
     */
    protected function lineHash(array $line): string
    {
        if (!\is_array($this->index)) {
            throw new InvalidOptionException('Invalid index', 1);
        }

        return \json_encode(
            \array_map(
                function (string $key) use ($line) {
                    if (!\array_key_exists($key, $line)) {
                        throw new UndefinedIndexException();
                    }

                    return $line[$key];
                },
                $this->index
            )
        );
    }
}

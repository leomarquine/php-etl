<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Extractors;

use Wizaplace\Etl\Exception\IncompleteDataException;
use Wizaplace\Etl\Exception\InvalidOptionException;
use Wizaplace\Etl\Exception\UndefinedIndexException;
use Wizaplace\Etl\Row;

class Aggregator extends Extractor
{
    public const DISCARD = 'discard';
    public const STRICT = 'strict';

    /**
     * The matching key tuple between iterators.
     */
    protected array $index = [];

    protected ?array $columns = null;

    /**
     * If set to true,
     * will throw a MissingDataException if there is any incomplete rows remaining
     * when all input iterators are fully consumed and closed.
     */
    protected bool $strict = true;

    /**
     * Discard incomplete rows
     */
    protected bool $discard = false;

    /** @var array[] */
    protected array $data = [];

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected array $availableOptions = [
        self::INDEX,
        self::COLUMNS,
        self::STRICT,
        self::DISCARD,
    ];

    /**
     * Properties that MUST be set via the options method.
     *
     * @var string[]
     */
    protected array $requiredOptions = [
        self::INDEX,
        self::COLUMNS,
    ];

    /**
     * @return \Generator<Row>
     *
     * @throws IncompleteDataException|InvalidOptionException
     */
    public function extract(): \Generator
    {
        // consume input iterators
        do {
            foreach ($this->input as $iterator) {
                $line = $iterator->current();

                if (true === \is_array($line)) {
                    $row = $this->build($line);
                    if (true === \is_array($row)) {
                        yield $this->defaultRow($row);
                    }
                }
                $iterator->next();
            }
        } while (
            $this->hasValidInput()
        );

        yield from $this->handleIncompleteRows();
    }

    /**
     * @return \Generator<Row>
     *
     * @throws IncompleteDataException
     */
    protected function handleIncompleteRows(): \Generator
    {
        $total = \count($this->data);

        if (0 === $total) {
            return;
        }

        if (true === $this->strict) {
            $plural = $total > 1;
            $message = '%d row%s %s rejected because incomplete';
            throw new IncompleteDataException(\sprintf($message, $total, $plural ? 's' : '', $plural ? 'were' : 'was'));
        }

        // then yield the incomplete remaining rows
        if (false === $this->discard) {
            foreach ($this->data as $row) {
                yield $this->defaultRow($row)->setIncomplete();
            }
        }
    }

    /**
     * Accumulate row data and return when completed.
     *
     * @param mixed[] $line
     *
     * @return mixed[]
     *
     * @throws InvalidOptionException
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
     *
     * @throws InvalidOptionException
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
     * Check if there are any opened iterators left.
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

    protected function defaultRow(array $data): Row
    {
        return new Row(
            \array_merge(
                \array_fill_keys(
                    $this->columns,
                    null
                ),
                $data
            )
        );
    }

    /**
     * Calculate row hash key from specified index array.
     *
     * @throws InvalidOptionException|UndefinedIndexException
     */
    protected function lineHash(array $line): string
    {
        if (0 === count($this->index)) {
            throw new InvalidOptionException('Index array is empty', 1);
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

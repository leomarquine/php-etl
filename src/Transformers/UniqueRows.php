<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Transformers;

use Wizaplace\Etl\Row;

class UniqueRows extends Transformer
{
    public const CONSECUTIVE = 'consecutive';

    /**
     * Transformer columns.
     *
     * @var string[]
     */
    protected array $columns = [];

    /**
     * Indicates if only consecutive duplicates will be removed.
     */
    protected bool $consecutive = false;

    /**
     * The control row for unique consecutive duplicates.
     *
     * @var string[]
     */
    protected array $control = [];

    /**
     * The hash table of the rows.
     *
     * @var string[]
     */
    protected array $hashTable = [];

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected array $availableOptions = [
        self::COLUMNS,
        self::CONSECUTIVE,
    ];

    /**
     * Transform the given row.
     */
    public function transform(Row $row): void
    {
        $subject = $this->prepare($row);

        if ($this->isDuplicate($subject)) {
            $row->discard();
        }

        $this->register($subject);
    }

    /**
     * Prepare the given row for comparison.
     *
     * @return mixed
     */
    protected function prepare(Row $row)
    {
        $row = $row->toArray();

        if ([] !== $this->columns) {
            $row = array_intersect_key($row, array_flip($this->columns));
        }

        return $this->consecutive ? $row : md5(serialize($row));
    }

    /**
     * Verify if the subject is duplicate.
     *
     * @param mixed $subject
     */
    protected function isDuplicate($subject): bool
    {
        return $this->consecutive ? $subject === $this->control : in_array($subject, $this->hashTable, true);
    }

    /**
     * Register the subject for future comparison.
     *
     * @param mixed $subject
     */
    protected function register($subject): void
    {
        $this->consecutive ? $this->control = $subject : $this->hashTable[] = $subject;
    }
}

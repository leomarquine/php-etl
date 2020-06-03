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
    /**
     * Transformer columns.
     *
     * @var string[]
     */
    protected $columns = [];

    /**
     * Indicates if only consecutive duplicates will be removed.
     *
     * @var bool
     */
    protected $consecutive = false;

    /**
     * The control row for unique consecutives.
     *
     * @var string[]
     */
    protected $control;

    /**
     * The hash table of the rows.
     *
     * @var string[]
     */
    protected $hashTable = [];

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected $availableOptions = [
        'columns', 'consecutive',
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

        if (null !== $this->columns) {
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

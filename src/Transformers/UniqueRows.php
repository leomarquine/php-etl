<?php

namespace Marquine\Etl\Transformers;

use Marquine\Etl\Row;

class UniqueRows extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var array
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
     * @var array
     */
    protected $control;

    /**
     * The hash table of the rows.
     *
     * @var array
     */
    protected $hashTable = [];

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns', 'consecutive'
    ];

    /**
     * Transform the given row.
     *
     * @param  \Marquine\Etl\Row  $row
     * @return void
     */
    public function transform(Row $row)
    {
        $subject = $this->prepare($row);

        if ($this->isDuplicate($subject)) {
            return $row->discard();
        }

        $this->register($subject);
    }

    /**
     * Prepare the given row for comparison.
     *
     * @param  array  $row
     * @return mixed
     */
    protected function prepare($row)
    {
        $row = $row->toArray();

        if (! empty($this->columns)) {
            $row = array_intersect_key($row, array_flip($this->columns));
        }

        return $this->consecutive ? $row : md5(serialize($row));
    }

    /**
     * Verify if the subject is duplicate.
     *
     * @param  mixed  $subject
     * @return bool
     */
    protected function isDuplicate($subject)
    {
        return $this->consecutive ? $subject === $this->control : in_array($subject, $this->hashTable);
    }

    /**
     * Register the subject for future comparison.
     *
     * @param  mixed  $subject
     * @return void
     */
    protected function register($subject)
    {
        $this->consecutive ? $this->control = $subject : $this->hashTable[] = $subject;
    }
}

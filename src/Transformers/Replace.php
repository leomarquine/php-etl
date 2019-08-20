<?php

namespace Marquine\Etl\Transformers;

use Marquine\Etl\Row;
use Marquine\Etl\Transformers\Transformer;
use InvalidArgumentException;

class Replace extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * The replace type.
     *
     * @var string
     */
    protected $type = 'str';

    /**
     * The string or the pattern to search for.
     *
     * @var string
     */
    protected $search = "";

    /**
     * The value to replace.
     *
     * @var string
     */
    protected $replace = "";

    /**
     * The replace function.
     *
     * @var string
     */
    protected $function;

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns', 'type', 'search', 'replace'
    ];

    /**
     * Initialize the step.
     *
     * @return void
     */
    public function initialize()
    {
        $this->function = $this->getReplaceFunction();
    }

    /**
     * Transform the given row.
     *
     * @param  \Marquine\Etl\Row  $row
     * @return void
     */
    public function transform(Row $row)
    {
        $row->transform($this->columns, function ($column) {
            return call_user_func($this->function, $this->search, $this->replace, $column);
        });
    }

    /**
     * Get the replace function name.
     *
     * @return string
     */
    protected function getReplaceFunction()
    {
        switch ($this->type) {
            case 'str':
                return 'str_replace';

            case 'preg':
                return 'preg_replace';
        }

        throw new InvalidArgumentException("The replace type [{$this->type}] is invalid.");
    }
}

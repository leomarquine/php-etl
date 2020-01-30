<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Wizaplace\Etl\Transformers;

use Wizaplace\Etl\Row;

class Replace extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var string[]
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
    protected $search = '';

    /**
     * The value to replace.
     *
     * @var string
     */
    protected $replace = '';

    /**
     * The replace function.
     *
     * @var string
     */
    protected $function;

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected $availableOptions = [
        'columns', 'type', 'search', 'replace',
    ];

    /**
     * Initialize the step.
     */
    public function initialize(): void
    {
        $this->function = $this->getReplaceFunction();
    }

    /**
     * Transform the given row.
     */
    public function transform(Row $row): void
    {
        $row->transform($this->columns, function ($column) {
            return call_user_func($this->function, $this->search, $this->replace, $column);
        });
    }

    /**
     * Get the replace function name.
     */
    protected function getReplaceFunction(): string
    {
        switch ($this->type) {
            case 'str':
                return 'str_replace';

            case 'preg':
                return 'preg_replace';
        }

        throw new \InvalidArgumentException("The replace type [{$this->type}] is invalid.");
    }
}

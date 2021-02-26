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

class Replace extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var string[]
     */
    protected array $columns = [];

    /**
     * The replace type ("str" or "preg").
     */
    protected string $type = 'str';

    /**
     * The string or the pattern to search for.
     */
    protected string $search = '';

    /**
     * The value to replace.
     */
    protected string $replace = '';

    /**
     * The replace function.
     */
    protected string $function = 'str_replace';

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected array $availableOptions = ['columns', 'type', 'search', 'replace'];

    public function initialize(): void
    {
        $this->function = $this->getReplaceFunction();
    }

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

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

class RenameColumns extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var string[]
     */
    protected $columns = [];

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected $availableOptions = [
        'columns',
    ];

    /**
     * Transform the given row.
     */
    public function transform(Row $row): void
    {
        foreach ($this->columns as $old => $new) {
            $value = $row->get($old);
            $row->remove($old);
            $row->set($new, $value);
        }
    }
}

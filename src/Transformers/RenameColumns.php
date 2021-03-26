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

class RenameColumns extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var string[]
     */
    protected array $columns = [];

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected array $availableOptions = [self::COLUMNS];

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

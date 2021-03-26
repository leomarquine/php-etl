<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @author      Karl DeBisschop <kdebisschop@gmail.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Transformers;

use Wizaplace\Etl\Row;

class CopyColumns extends Transformer
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

    public function transform(Row $row): void
    {
        foreach ($this->columns as $old => $new) {
            $value = $row->get($old);
            $row->set($new, $value);
        }
    }
}

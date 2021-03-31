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

class RowCallback extends Transformer
{
    public const CALLBACK = 'callback';

    /**
     * The callback function.
     *
     * @var callable
     */
    protected $callback;

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected array $availableOptions = [
        self::CALLBACK,
    ];

    /**
     * Transform the given row.
     */
    public function transform(Row $row): void
    {
        ($this->callback)($row);
    }
}

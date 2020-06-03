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
use Wizaplace\Etl\Step;

abstract class Transformer extends Step
{
    /**
     * Transform the given row.
     */
    abstract public function transform(Row $row): void;
}

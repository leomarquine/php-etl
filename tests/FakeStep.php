<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests;

use Wizaplace\Etl\Step;

class FakeStep extends Step
{
    protected $option1;
    protected $option2;
    protected $availableOptions = ['option1'];

    public function getOption(string $name)
    {
        $name = lcfirst($name);

        return $this->$name ?? null;
    }
}

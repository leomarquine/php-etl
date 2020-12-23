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
    /** @var string */
    protected $option1;

    /** @var string */
    protected $option2;

    /** @var string[] */
    protected $availableOptions = ['option1'];

    public function getOption(string $name): ?string
    {
        $name = lcfirst($name);

        switch ($name) {
            case 'option1':
                return $this->option1 ?? null;
            case 'option2':
                return $this->option2 ?? null;
            default:
                return null;
        }
    }
}

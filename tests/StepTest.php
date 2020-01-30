<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Tests;

use Wizaplace\Etl\Step;

class StepTest extends TestCase
{
    /** @test */
    public function set_options()
    {
        $step = new FakeStep();

        $step->options([
            'option1' => 'value',
            'option2' => 'value',
        ]);

        static::assertEquals('value', $step->getOption('Option1'));
        static::assertNull($step->getOption('Option2'));
    }
}

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

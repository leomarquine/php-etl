<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests;

class StepTest extends TestCase
{
    /** @test */
    public function setOptions(): void
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

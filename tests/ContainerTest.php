<?php

namespace Tests;

use Marquine\Etl\Container;

class ContainerTest extends TestCase
{
    /** @test */
    public function make_a_step_using_its_container_bind()
    {
        $container = new Container;

        $container->bind('valid_step_abstractstep', ValidStep::class);

        $step = $container->step('valid_step', AbstractStep::class);

        $this->assertInstanceOf(ValidStep::class, $step);
    }

    /** @test */
    public function make_a_step_using_a_class_string()
    {
        $container = new Container;

        $step = $container->step(ValidStep::class, AbstractStep::class);

        $this->assertInstanceOf(ValidStep::class, $step);
    }

    /** @test */
    public function make_a_step_using_an_instance()
    {
        $container = new Container;

        $step = $container->step(new ValidStep, AbstractStep::class);

        $this->assertInstanceOf(ValidStep::class, $step);
    }

    /** @test */
    public function throws_exception_for_invalid_step_class()
    {
        $container = new Container;

        $name = InvalidStep::class;

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage("The step [$name] is not a valid abstractstep.");

        $container->step(InvalidStep::class, AbstractStep::class);
    }

    /** @test */
    public function throws_exception_for_invalid_step_bind()
    {
        $container = new Container;

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage("The step [invalid_step] is not a valid abstractstep.");

        $container->step('invalid_step', AbstractStep::class);
    }
}


abstract class AbstractStep
{
    //
}

class ValidStep extends AbstractStep
{
    //
}

class InvalidStep
{
    //
}

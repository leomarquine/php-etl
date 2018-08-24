<?php

namespace Tests;

use Marquine\Etl\Factory;
use InvalidArgumentException;

class FactoryTest extends TestCase
{
    /** @test */
    public function creates_a_step_instance_from_a_class_string()
    {
        $factory = new Factory;

        $step = $factory->make(SimpleValidStep::class, StepInterface::class, []);

        $this->assertInstanceOf(SimpleValidStep::class, $step);
    }

    /** @test */
    public function guesses_the_step_class_based_on_the_given_interface()
    {
        $factory = new Factory;

        $this->assertInstanceOf(SimpleValidStep::class, $factory->make('simpleValidStep', StepInterface::class, []));
        $this->assertInstanceOf(SimpleValidStep::class, $factory->make('simple valid step', StepInterface::class, []));
        $this->assertInstanceOf(SimpleValidStep::class, $factory->make('simple_valid_step', StepInterface::class, []));
        $this->assertInstanceOf(SimpleValidStep::class, $factory->make('simple-valid-step', StepInterface::class, []));
    }

    /** @test */
    public function throws_exception_if_the_given_step_does_not_exist()
    {
        $factory = new Factory;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Tests\DoesNotExist is not a valid '".__FUNCTION__."' step.");

        $factory->make('does not exist', StepInterface::class, []);
    }

    /** @test */
    public function recursively_build_step_dependencies()
    {
        $factory = new Factory;

        $step = $factory->make('valid step with dependencies', StepInterface::class, []);

        $this->assertInstanceOf(ValidStepWithDependencies::class, $step);
        $this->assertInstanceOf(Foo::class, $step->foo);
        $this->assertInstanceOf(Bar::class, $step->foo->bar);
    }

    /** @test */
    public function throws_exception_if_step_is_invalid()
    {
        $factory = new Factory;

        $this->expectException(InvalidArgumentException::class);

        $factory->make(InvalidStep::class, StepInterface::class, []);
    }

    /** @test */
    public function set_provided_step_options()
    {
        $factory = new Factory;

        $options = [
            'configOption1' => 'value1',
            'config option2' => 'value2',
            'config_option3' => 'value3',
            'config-option4' => 'value4',
        ];

        $step = $factory->make(SimpleValidStep::class, StepInterface::class, $options);

        $this->assertEquals('value1', $step->configOption1);
        $this->assertEquals('value2', $step->configOption2);
        $this->assertEquals('value3', $step->configOption3);
        $this->assertEquals('value4', $step->configOption4);
    }
}

class Bar {}

class Foo
{
    public $bar;

    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }
}

interface StepInterface {}

class InvalidStep {}

class SimpleValidStep implements StepInterface
{
    public $configOption1;
    public $configOption2;
    public $configOption3;
    public $configOption4;
}

class ValidStepWithDependencies implements StepInterface
{
    public $foo;

    public function __construct(Foo $foo)
    {
        $this->foo = $foo;
    }
}

<?php

namespace Tests;

use Tests\TestCase;
use Marquine\Etl\Factory;
use InvalidArgumentException;

class FactoryTest extends TestCase
{
    /** @test */
    function create_a_new_step_instance_and_set_options()
    {
        $factory = new Factory;

        $instance = $factory->make(FakeBaseStep::class, 'FakeStep', ['option' => 'value']);

        $this->assertInstanceOf(FakeStep::class, $instance);
        $this->assertEquals('value', $instance->option);
    }

    /** @test */
    function normalize_step_name()
    {
        $factory = new Factory;

        $this->assertInstanceOf(FakeStep::class, $factory->make(FakeBaseStep::class, 'fakeStep'));
        $this->assertInstanceOf(FakeStep::class, $factory->make(FakeBaseStep::class, 'fake-step'));
        $this->assertInstanceOf(FakeStep::class, $factory->make(FakeBaseStep::class, 'fake_step'));
        $this->assertInstanceOf(FakeStep::class, $factory->make(FakeBaseStep::class, 'fake step'));
    }

    /** @test */
    function throws_an_exception_for_invalid_step()
    {
        $factory = new Factory;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("AnotherFakeStep is not a valid '".__FUNCTION__."' step."); // name from caller's method name

        $factory->make(FakeBaseStep::class, 'AnotherFakeStep');
    }

    /** @test */
    function throws_an_exception_for_inexistent_class()
    {
        $factory = new Factory;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("InexistentFakeStep is not a valid '".__FUNCTION__."' step."); // name from caller's method name

        $factory->make(FakeBaseStep::class, 'InexistentFakeStep');
    }
}

abstract class FakeBaseStep {}

class FakeStep extends FakeBaseStep { public $option; }

class AnotherFakeStep {}

<?php

namespace Tests;

use Tests\TestCase;
use Marquine\Etl\Factory;
use InvalidArgumentException;
use Marquine\Etl\Loaders\LoaderInterface;
use Marquine\Etl\Extractors\ExtractorInterface;
use Marquine\Etl\Transformers\TransformerInterface;

class FactoryTest extends TestCase
{
    /** @test */
    function create_a_new_step_instance_and_set_options()
    {
        $factory = new Factory;

        $instance = $factory->make(FakeStepInterface::class, 'FakeStep', ['option' => 'value']);

        $this->assertInstanceOf(FakeStep::class, $instance);
        $this->assertEquals('value', $instance->option);
    }

    /** @test */
    function normalize_step_name()
    {
        $factory = new Factory;

        $this->assertInstanceOf(FakeStep::class, $factory->make(FakeStepInterface::class, 'fakeStep'));
        $this->assertInstanceOf(FakeStep::class, $factory->make(FakeStepInterface::class, 'fake-step'));
        $this->assertInstanceOf(FakeStep::class, $factory->make(FakeStepInterface::class, 'fake_step'));
        $this->assertInstanceOf(FakeStep::class, $factory->make(FakeStepInterface::class, 'fake step'));
    }

    /** @test */
    function throws_an_exception_for_invalid_step()
    {
        $factory = new Factory;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("AnotherFakeStep is not a valid '".__FUNCTION__."' step."); // name from caller's method name

        $factory->make(FakeStepInterface::class, 'AnotherFakeStep');
    }

    /** @test */
    function throws_an_exception_for_inexistent_class()
    {
        $factory = new Factory;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("InexistentFakeStep is not a valid '".__FUNCTION__."' step."); // name from caller's method name

        $factory->make(FakeStepInterface::class, 'InexistentFakeStep');
    }
}

interface FakeStepInterface {}

class FakeStep implements FakeStepInterface { public $option; }

class AnotherFakeStep {}

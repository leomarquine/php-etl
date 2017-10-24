<?php

namespace Tests;

use Mockery;
use Marquine\Etl\Job;
use Marquine\Etl\Factory;
use Marquine\Etl\Pipeline;
use Marquine\Etl\Loaders\Loader;
use Marquine\Etl\Extractors\Extractor;
use Marquine\Etl\Transformers\Transformer;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class JobTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    function job()
    {
        $factory = Mockery::mock(Factory::class);
        $pipeline = Mockery::mock(Pipeline::class);
        $extractor = Mockery::mock(Extractor::class);
        $transformer = Mockery::mock(Transformer::class);
        $loader = Mockery::mock(Loader::class);

        $handler = function () {};
        $generator = function () { yield 'data'; };
        $generator = $generator();

        $job = new Job;
        $job->setFactory($factory);

        $factory->shouldReceive('make')->once()->with(Extractor::class, 'extractor-name', ['options'])->andReturn($extractor);
        $extractor->shouldReceive('pipeline')->once()->with('source')->andReturn($pipeline);
        $job->extract('extractor-name', 'source', ['options']);

        $factory->shouldReceive('make')->once()->with(Transformer::class, 'transformer-name', ['options'])->andReturn($transformer);
        $transformer->shouldReceive('handler')->once()->withNoArgs()->andReturn($handler);
        $pipeline->shouldReceive('pipe')->once()->with($handler);
        $job->transform('transformer-name', ['options']);

        $factory->shouldReceive('make')->once()->with(Loader::class, 'loader-name', ['options'])->andReturn($loader);
        $pipeline->shouldReceive('get')->once()->withNoArgs()->andReturn($generator);
        $loader->shouldReceive('load')->once()->with($generator, 'destination');
        $job->load('loader-name', 'destination', ['options']);

        $pipeline->shouldReceive('get')->once()->withNoArgs()->andReturn($generator);
        $this->assertEquals(['data'], $job->toArray());
    }
}

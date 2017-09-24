<?php

namespace Tests;

use Mockery;
use Generator;
use Marquine\Etl\Job;
use Marquine\Etl\Pipeline;
use Marquine\Etl\Loaders\LoaderInterface ;
use Marquine\Etl\Extractors\ExtractorInterface;
use Marquine\Etl\Transformers\TransformerInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class JobTest extends TestCase
{
    use MockeryPHPUnitIntegration;

     /** @test */
    function new_job_instance_with_static_extract_call()
    {
        $extractor = Mockery::mock(ExtractorInterface::class);
        $extractor->shouldReceive('extract')->with('source')->once()->andReturn((function () { yield ' foo '; })());

        $this->assertInstanceOf(Job::class, Job::extract($extractor, 'source'));
    }

    /** @test */
    function job_execution()
    {
        $job = new Job;

        $extractor = Mockery::mock(ExtractorInterface::class);
        $extractor->shouldReceive('extract')->with('source')->once()->andReturn((function () { yield ' foo '; })());

        $this->assertInstanceOf(Job::class, $job->extract($extractor, 'source'));

        $this->assertInstanceOf(Pipeline::class, $this->readAttribute($job, 'pipeline'));

        $transformer = Mockery::mock(TransformerInterface::class);
        $transformer->shouldReceive('handle')->once()->andReturn(function ($row) { return trim($row); });

        $this->assertInstanceOf(Job::class, $job->transform($transformer));

        $loader = Mockery::mock(LoaderInterface::class);
        $loader->shouldReceive('load')->with('destination', $this->readAttribute($job, 'pipeline'))->once();

        $this->assertInstanceOf(Job::class, $job->load($loader, 'destination'));

        $this->assertInstanceOf(Generator::class, $job->data());
        $this->assertEquals('foo', $job->toArray()[0]);
    }
}

<?php

namespace Tests\Extractors;

use Mockery;
use Tests\TestCase;
use Marquine\Etl\Pipeline;
use Marquine\Etl\Extractors\Extractor;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class ExtractorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function get_the_pipeline_for_the_extractor()
    {
        $generator = function () {
            yield 'data';
        };

        $extractor = Mockery::mock(Extractor::class);
        $extractor->shouldReceive('extract')->with('source')->andReturn($generator());
        $extractor->shouldReceive('pipeline')->once()->passthru();

        $this->assertInstanceOf(Pipeline::class, $extractor->pipeline('source'));
    }
}

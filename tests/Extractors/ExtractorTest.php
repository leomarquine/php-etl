<?php

namespace Tests\Extractors;

use Mockery;
use Tests\TestCase;
use Marquine\Etl\Pipeline;
use Marquine\Etl\Extractors\Extractor;
use Marquine\Etl\Exceptions\FileNotFoundException;
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

    /** @test */
    public function validates_a_source_file()
    {
        $extractor = Mockery::mock(Extractor::class);

        $source = $extractor->validateSourceFile('csv1.csv');
        $this->assertTrue(is_file($source));

        $source = $extractor->validateSourceFile(__DIR__.'/../data/csv1.csv');
        $this->assertTrue(is_file($source));

        $source = $extractor->validateSourceFile('http://leomarquine.com');
        $this->assertEquals('http://leomarquine.com', $source);

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage("The file 'invalid-file' was not found.");
        $extractor->validateSourceFile('invalid-file');
    }
}

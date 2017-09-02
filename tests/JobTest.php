<?php

namespace Tests;

use Marquine\Etl\Job;
use Marquine\Etl\Loaders\LoaderInterface ;
use Marquine\Etl\Extractors\ExtractorInterface;
use Marquine\Etl\Transformers\TransformerInterface;

class JobTest extends TestCase
{
    /** @test */
    function extract()
    {
        $job = new Job;

        $extractor = new class implements ExtractorInterface {
            public $property;
            public $called = false;
            public function extract($source) { $this->called = true; }
        };

        $job->extract($extractor, 'source', ['property' => 'value']);

        $this->assertTrue($extractor->called);
        $this->assertEquals($extractor->property, 'value');
    }

    /** @test */
    function transform()
    {
        $job = new Job;

        $transformer = new class implements TransformerInterface {
            public $property;
            public $called = false;
            public function transform($items) { $this->called = true; }
        };

        $job->transform($transformer, ['property' => 'value']);

        $this->assertTrue($transformer->called);
        $this->assertEquals($transformer->property, 'value');
    }

    /** @test */
    function load()
    {
        $job = new Job;

        $loader = new class implements LoaderInterface {
            public $property;
            public $called = false;
            public function load($destination, $items) { $this->called = true; }
        };

        $job->load($loader, 'destination', ['property' => 'value']);

        $this->assertTrue($loader->called);
        $this->assertEquals($loader->property, 'value');
    }
}

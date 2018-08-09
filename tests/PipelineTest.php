<?php

namespace Tests;

use Generator;
use IteratorAggregate;
use Marquine\Etl\Pipeline;

class PipelineTest extends TestCase
{
    protected $flow;

    protected function setUp()
    {
        parent::setUp();

        $this->flow = new class implements IteratorAggregate {
            public function getIterator()
            {
                yield 'row1';
                yield 'row2';
            }
        };
    }

    /** @test */
    public function pipeline_flow()
    {
        $pipeline = new Pipeline($this->flow);

        $generator = $pipeline->pipe(function ($row) {
            return "*{$row}*";
        })->get();

        $this->assertInstanceOf(Generator::class, $generator);

        $this->assertEquals(['*row1*', '*row2*'], iterator_to_array($generator));
    }
}

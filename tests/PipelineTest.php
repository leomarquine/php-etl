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
    public function pipeline_flow_and_metadata()
    {
        $pipeline = new Pipeline;
        $pipeline->flow($this->flow);

        $generator = $pipeline->pipe(function ($row, $meta) {
            $this->assertEquals($meta, ['total' => 2, 'current' => substr($row, -1)]);

            return "*{$row}*";
        })->get();

        $this->assertInstanceOf(Generator::class, $generator);

        $this->assertEquals(['*row1*', '*row2*'], iterator_to_array($generator));
    }

    /** @test */
    public function tasks_can_be_run_before_the_pipeline_flow_starts()
    {
        $control = false;

        $callback = function () use (&$control) {
            $control = true;
        };

        $pipeline = new Pipeline;
        $pipeline->flow($this->flow);

        $generator = $pipeline->registerPreExecutionTask($callback)->get();

        $generator->rewind();

        $this->assertTrue($control);
    }

    /** @test */
    public function tasks_can_be_run_after_the_pipeline_flow_ends()
    {
        $control = false;

        $callback = function () use (&$control) {
            $control = true;
        };

        $pipeline = new Pipeline;
        $pipeline->flow($this->flow);

        $generator = $pipeline->registerPostExecutionTask($callback)->get();

        while ($generator->valid()) {
            $this->assertFalse($control);
            $generator->next();
        }

        $this->assertTrue($control);
    }

    /** @test */
    public function maximum_number_of_rows_can_be_limited()
    {
        $pipeline = new Pipeline;
        $pipeline->flow($this->flow);

        $generator = $pipeline->limit(1)->get();

        $this->assertEquals(['row1'], iterator_to_array($generator));
    }

    /** @test */
    public function initial_rows_can_be_skipped()
    {
        $pipeline = new Pipeline;
        $pipeline->flow($this->flow);

        $generator = $pipeline->skip(1)->get();

        $this->assertEquals(['row2'], iterator_to_array($generator));
    }
}

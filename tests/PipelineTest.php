<?php

namespace Tests;

use Generator;
use IteratorAggregate;
use Marquine\Etl\Pipeline;

class PipelineTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->pipeline = new Pipeline;
        $this->pipeline->flow(new class implements IteratorAggregate
        {
            public function getIterator()
            {
                yield 'row1';
                yield 'row2';
                yield 'row3';
            }
        });
    }

    /** @test */
    public function pipeline_flow_and_metadata()
    {
        $generator = $this->pipeline->pipe(function ($row) {
            $this->assertEquals($this->pipeline->metadata('current'), substr($row, -1));

            return "*{$row}*";
        })->get();

        $this->assertInstanceOf(Generator::class, $generator);

        $this->assertEquals(['*row1*', '*row2*', '*row3*'], iterator_to_array($generator));

        $this->assertEquals($this->pipeline->metadata('total'), 3);
        $this->assertTrue(is_object($this->pipeline->metadata()));
    }

    /** @test */
    public function tasks_can_be_run_before_the_pipeline_flow_starts()
    {
        $control = false;

        $callback = function () use (&$control) {
            $control = true;
        };

        $generator = $this->pipeline->before($callback)->get();

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

        $generator = $this->pipeline->after($callback)->get();

        while ($generator->valid()) {
            $this->assertFalse($control);
            $generator->next();
        }

        $this->assertTrue($control);
    }

    /** @test */
    public function maximum_number_of_rows_can_be_limited()
    {
        $generator = $this->pipeline->limit(1)->get();

        $this->assertEquals(['row1'], iterator_to_array($generator));
    }

    /** @test */
    public function initial_rows_can_be_skipped()
    {
        $generator = $this->pipeline->skip(1)->get();

        $this->assertEquals(['row2', 'row3'], iterator_to_array($generator));
    }

    /** @test */
    public function maximum_number_of_rows_should_not_count_skipped_rows()
    {
        $generator = $this->pipeline->skip(1)->limit(1)->get();

        $this->assertEquals(['row2'], iterator_to_array($generator));
    }

    /** @test */
    public function total_rows_and_current_row_must_not_count_skipped_rows()
    {
        $generator = $this->pipeline->skip(2)->get();

        $this->assertEquals(['row3'], iterator_to_array($generator));

        $this->assertEquals(1, $this->pipeline->metadata('total'));
        $this->assertEquals(1, $this->pipeline->metadata('current'));
    }

    /** @test */
    public function total_rows_must_not_be_greater_then_row_limit()
    {
        $generator = $this->pipeline->limit(1)->get();

        $this->assertEquals(['row1'], iterator_to_array($generator));

        $this->assertEquals(1, $this->pipeline->metadata('total'));
        $this->assertEquals(1, $this->pipeline->metadata('current'));
    }

    /** @test */
    public function provides_the_first_row_as_sample()
    {
        $this->assertEquals('row1', $this->pipeline->sample());
    }

    /** @test */
    public function empty_rows_will_be_skipped()
    {
        $generator = $this->pipeline->pipe(function ($row) {
            return $row == 'row2' ? null : $row;
        })->get();

        $this->assertEquals(['row1', 'row3'], iterator_to_array($generator));
    }
}

<?php

namespace Tests;

use Generator;
use Marquine\Etl\Pipeline;

class PipelineTest extends TestCase
{
    /** @test */
    public function it_pipes_tasks_into_generators()
    {
        $pipeline = new Pipeline($this->generator());

        $generator = $pipeline->pipe('strtolower')->pipe('trim')->get();

        $this->assertInstanceOf(Generator::class, $generator);

        $this->assertEquals(['foo', 'bar'], iterator_to_array($generator));
    }

    protected function generator()
    {
        yield 'FOO ';
        yield ' BAR';
    }
}

<?php

namespace Tests;

use Marquine\Etl\Flow;

class FlowTest extends TestCase
{
    /** @test */
    public function flow_must_implement_iterator_aggregate()
    {
        $flow = new Flow([]);

        $this->assertInstanceOf('IteratorAggregate', $flow);
    }

    /** @test */
    public function get_iterator_must_be_a_generator()
    {
        $flow = new Flow([1, 2]);

        $this->assertInstanceOf('Generator', $flow->getIterator());
        $this->assertEquals([1, 2], iterator_to_array($flow));
    }
}

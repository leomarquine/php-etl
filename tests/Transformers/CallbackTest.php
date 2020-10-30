<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Etl\Transformers\Callback;

class CallbackTest extends TestCase
{
    /** @test */
    public function transform_data_using_a_callback()
    {
        $transformer = new Callback;

        $row = $this->createMock('Marquine\Etl\Row');

        $callback = $this->getMockBuilder('stdClass')->setMethods(['callback'])->getMock();
        $callback->expects($this->once())->method('callback')->with($row)->willReturn('asd');

        $transformer->options(['callback' => [$callback, 'callback']]);

        $transformer->transform($row);
    }
}

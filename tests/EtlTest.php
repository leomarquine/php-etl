<?php

namespace Tests;

use Marquine\Etl\Etl;
use Marquine\Etl\Container;

class EtlTest extends TestCase
{
    /** @test */
    public function extract_step()
    {
        $container = $this->createMock('Marquine\Etl\Container');
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');
        $extractor = $this->createMock('Marquine\Etl\Extractors\Extractor');
        $flow = $this->createMock('Marquine\Etl\Flow');

        $container->expects($this->once())->method('step')->with('step_name', 'Marquine\Etl\Extractors\Extractor')->willReturn($extractor);
        $container->expects($this->once())->method('make')->with('Marquine\Etl\Flow', [])->willReturn($flow);

        $pipeline->expects($this->once())->method('flow')->with($flow);

        $extractor->expects($this->once())->method('pipeline')->with($pipeline)->willReturnSelf();
        $extractor->expects($this->once())->method('options')->with(['options'])->willReturnSelf();
        $extractor->expects($this->once())->method('extract')->with('source')->willReturn([]);

        $etl = new Etl($container, $pipeline);

        $this->assertInstanceOf(Etl::class, $etl->extract('step_name', 'source', ['options']));
    }

    /** @test */
    public function transform_step()
    {
        $container = $this->createMock('Marquine\Etl\Container');
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');
        $transformer = $this->createMock('Marquine\Etl\Transformers\Transformer');

        $container->expects($this->once())->method('step')->with('step_name', 'Marquine\Etl\Transformers\Transformer')->willReturn($transformer);

        $pipeline->expects($this->once())->method('pipe')->with(function () {});

        $transformer->expects($this->once())->method('pipeline')->with($pipeline)->willReturnSelf();
        $transformer->expects($this->once())->method('options')->with(['options'])->willReturnSelf();
        $transformer->expects($this->once())->method('transform')->with()->willReturn(function () {});

        $etl = new Etl($container, $pipeline);

        $this->assertInstanceOf(Etl::class, $etl->transform('step_name', ['options']));
    }

    /** @test */
    public function load_step()
    {
        $container = $this->createMock('Marquine\Etl\Container');
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');
        $loader = $this->createMock('Marquine\Etl\Loaders\Loader');

        $container->expects($this->once())->method('step')->with('step_name', 'Marquine\Etl\Loaders\Loader')->willReturn($loader);

        $pipeline->expects($this->once())->method('pipe')->with(function () {});

        $loader->expects($this->once())->method('pipeline')->with($pipeline)->willReturnSelf();
        $loader->expects($this->once())->method('options')->with(['options'])->willReturnSelf();
        $loader->expects($this->once())->method('load')->with('destination')->willReturn(function () {});

        $etl = new Etl($container, $pipeline);

        $this->assertInstanceOf(Etl::class, $etl->load('step_name', 'destination', ['options']));
    }

    /** @test */
    public function run_the_etl()
    {
        $generator = $this->createMock('Iterator');
        $generator->expects($this->exactly(3))->method('valid')->willReturnOnConsecutiveCalls(true, true, false);
        $generator->expects($this->exactly(2))->method('next');

        $pipeline = $this->createMock('Marquine\Etl\Pipeline');
        $pipeline->expects($this->once())->method('get')->willReturn($generator);

        $container = $this->createMOck('Marquine\Etl\Container');

        $etl = new Etl($container, $pipeline);

        $etl->run();
    }

    /** @test */
    public function get_an_array_of_the_etl_data()
    {
        $generator = $this->createMock('Iterator');
        $generator->expects($this->exactly(3))->method('valid')->willReturnOnConsecutiveCalls(true, true, false);
        $generator->expects($this->exactly(2))->method('key')->willReturnOnConsecutiveCalls(0, 1);
        $generator->expects($this->exactly(2))->method('current')->willReturnOnConsecutiveCalls('row1', 'row2');
        $generator->expects($this->exactly(2))->method('next');

        $pipeline = $this->createMock('Marquine\Etl\Pipeline');
        $pipeline->expects($this->once())->method('get')->willReturn($generator);

        $container = $this->createMOck('Marquine\Etl\Container');

        $etl = new Etl($container, $pipeline);

        $this->assertEquals(['row1', 'row2'], $etl->toArray());
    }

    /** @test */
    public function get_a_service_from_the_container()
    {
        $container = $this->createMock('Marquine\Etl\Container');
        $container->expects($this->once())->method('make')->with('service')->willReturn('instance');

        Container::setInstance($container);

        $this->assertEquals('instance', Etl::service('service'));
    }
}

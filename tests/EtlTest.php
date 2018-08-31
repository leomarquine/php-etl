<?php

namespace Tests;

use Marquine\Etl\Etl;
use Marquine\Etl\Container;

class EtlTest extends TestCase
{
    /** @test */
    public function extract_step()
    {
        $extractor = $this->createMock('Marquine\Etl\Extractors\ExtractorInterface');
        $extractor->expects($this->once())->method('source')->with('source');

        $container = $this->createMOck('Marquine\Etl\Container');
        $container->expects($this->once())->method('make')->with('extractor.step_name')->willReturn($extractor);

        $pipeline = $this->createMock('Marquine\Etl\Pipeline');
        $pipeline->expects($this->once())->method('flow')->with($extractor);

        $etl = new Etl($container, $pipeline);

        $etl->extract('step_name', 'source', ['options']);
    }

    /** @test */
    public function transform_step()
    {
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');
        $transformer = $this->createMock('Marquine\Etl\Transformers\TransformerInterface');

        $pipeline->expects($this->once())->method('pipe')->with(function () {});
        $transformer->expects($this->once())->method('handler')->with($pipeline)->willReturn(function () {});

        $container = $this->createMOck('Marquine\Etl\Container');
        $container->expects($this->once())->method('make')->with('transformer.step_name')->willReturn($transformer);

        $etl = new Etl($container, $pipeline);

        $etl->transform('step_name', ['options']);
    }

    /** @test */
    public function load_step()
    {
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');
        $loader = $this->createMock('Marquine\Etl\Loaders\LoaderInterface');

        $pipeline->expects($this->once())->method('pipe')->with(function () {});
        $loader->expects($this->once())->method('handler')->with($pipeline, 'destination')->willReturn(function () {});

        $container = $this->createMOck('Marquine\Etl\Container');
        $container->expects($this->once())->method('make')->with('loader.step_name')->willReturn($loader);

        $etl = new Etl($container, $pipeline);

        $etl->load('step_name', 'destination', ['options']);
    }

    /** @test */
    public function execute_the_etl()
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

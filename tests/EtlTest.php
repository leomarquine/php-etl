<?php

namespace Tests;

use Marquine\Etl\Etl;
use Marquine\Etl\Container;
use Marquine\Etl\Extractors\Extractor;

class EtlTest extends TestCase
{
    /** @test */
    public function extract_step()
    {
        $extractor = $this->createMock('Marquine\Etl\Extractors\Extractor');
        $extractor->expects($this->once())->method('input')->with('input')->willReturnSelf();
        $extractor->expects($this->once())->method('options')->with(['options']);

        $pipeline = $this->createMock('Marquine\Etl\Pipeline');
        $pipeline->expects($this->once())->method('extractor')->with($extractor);

        $etl = new Etl($pipeline);

        $this->assertInstanceOf(Etl::class, $etl->extract($extractor, 'input', ['options']));
    }

    /** @test */
    public function transform_step()
    {
        $transformer = $this->createMock('Marquine\Etl\Transformers\Transformer');
        $transformer->expects($this->once())->method('options')->with(['options']);

        $pipeline = $this->createMock('Marquine\Etl\Pipeline');
        $pipeline->expects($this->once())->method('pipe')->with($transformer);

        $etl = new Etl($pipeline);

        $this->assertInstanceOf(Etl::class, $etl->transform($transformer, ['options']));
    }

    /** @test */
    public function load_step()
    {
        $loader = $this->createMock('Marquine\Etl\Loaders\Loader');
        $loader->expects($this->once())->method('output')->with('output')->willReturnSelf();
        $loader->expects($this->once())->method('options')->with(['options']);

        $pipeline = $this->createMock('Marquine\Etl\Pipeline');
        $pipeline->expects($this->once())->method('pipe')->with($loader);

        $etl = new Etl($pipeline);

        $this->assertInstanceOf(Etl::class, $etl->load($loader, 'output', ['options']));
    }

    /** @test */
    public function run_the_etl()
    {
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');
        $pipeline->expects($this->exactly(1))->method('rewind');
        $pipeline->expects($this->exactly(3))->method('valid')->willReturnOnConsecutiveCalls(true, true, false);
        $pipeline->expects($this->exactly(2))->method('next');

        $etl = new Etl($pipeline);

        $etl->run();
    }

    /** @test */
    public function get_an_array_of_the_etl_data()
    {
        $pipeline = $this->createMock('Marquine\Etl\Pipeline');
        $pipeline->expects($this->exactly(3))->method('valid')->willReturnOnConsecutiveCalls(true, true, false);
        $pipeline->expects($this->exactly(2))->method('key')->willReturnOnConsecutiveCalls(0, 1);
        $pipeline->expects($this->exactly(2))->method('current')->willReturnOnConsecutiveCalls('row1', 'row2');
        $pipeline->expects($this->exactly(2))->method('next');

        $etl = new Etl($pipeline);

        $this->assertEquals(['row1', 'row2'], $etl->toArray());
    }
}

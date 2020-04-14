<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Tests;

use Wizaplace\Etl\Etl;
use Wizaplace\Etl\Row;

class EtlTest extends TestCase
{
    /** @test */
    public function extract_step()
    {
        $extractor = $this->createMock('Wizaplace\Etl\Extractors\Extractor');
        $extractor->expects($this->once())->method('input')->with('input')->willReturnSelf();
        $extractor->expects($this->once())->method('options')->with(['options']);

        $pipeline = $this->createMock('Wizaplace\Etl\Pipeline');
        $pipeline->expects($this->once())->method('extractor')->with($extractor);

        $etl = new Etl($pipeline);

        static::assertInstanceOf(Etl::class, $etl->extract($extractor, 'input', ['options']));
    }

    /** @test */
    public function transform_step()
    {
        $transformer = $this->createMock('Wizaplace\Etl\Transformers\Transformer');
        $transformer->expects($this->once())->method('options')->with(['options']);

        $pipeline = $this->createMock('Wizaplace\Etl\Pipeline');
        $pipeline->expects($this->once())->method('pipe')->with($transformer);

        $etl = new Etl($pipeline);

        static::assertInstanceOf(Etl::class, $etl->transform($transformer, ['options']));
    }

    /** @test */
    public function load_step()
    {
        $loader = $this->createMock('Wizaplace\Etl\Loaders\Loader');
        $loader->expects($this->once())->method('output')->with('output')->willReturnSelf();
        $loader->expects($this->once())->method('options')->with(['options']);

        $pipeline = $this->createMock('Wizaplace\Etl\Pipeline');
        $pipeline->expects($this->once())->method('pipe')->with($loader);

        $etl = new Etl($pipeline);

        static::assertInstanceOf(Etl::class, $etl->load($loader, 'output', ['options']));
    }

    /** @test */
    public function run_the_etl()
    {
        $pipeline = $this->createMock('Wizaplace\Etl\Pipeline');
        $pipeline->expects($this->exactly(1))->method('rewind');
        $pipeline->expects($this->exactly(3))->method('valid')->willReturnOnConsecutiveCalls(true, true, false);
        $pipeline->expects($this->exactly(2))->method('next');

        $etl = new Etl($pipeline);

        $etl->run();
    }

    /** @test */
    public function get_an_array_of_the_etl_data()
    {
        $pipeline = $this->createMock('Wizaplace\Etl\Pipeline');
        $pipeline->expects($this->exactly(4))->method('valid')->willReturnOnConsecutiveCalls(true, true, true, false);
        $pipeline->expects($this->exactly(3))->method('current')->willReturnOnConsecutiveCalls(
            new Row(['row1']),
            (new Row(['row2']))->discard(),
            new Row(['row3'])
        );
        $pipeline->expects($this->exactly(3))->method('next');

        $etl = new Etl($pipeline);

        static::assertEquals([['row1'], ['row3']], $etl->toArray());
    }
}

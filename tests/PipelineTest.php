<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Tests;

use Wizaplace\Etl\Pipeline;

class PipelineTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->row1 = $this->createMock('Wizaplace\Etl\Row');
        $this->row1->expects($this->any())->method('toArray')->willReturn(['row1']);

        $this->row2 = $this->createMock('Wizaplace\Etl\Row');
        $this->row2->expects($this->any())->method('toArray')->willReturn(['row2']);

        $this->row3 = $this->createMock('Wizaplace\Etl\Row');
        $this->row3->expects($this->any())->method('toArray')->willReturn(['row3']);

        $generator = function () {
            yield $this->row1;
            yield $this->row2;
            yield $this->row3;
        };

        $this->extractor = $this->createMock('Wizaplace\Etl\Extractors\Extractor');
        $this->extractor->expects($this->any())->method('extract')->willReturn($generator());

        $this->transformer = $this->createMock('Wizaplace\Etl\Transformers\Transformer');
        $this->transformer->expects($this->any())->method('transform')->withConsecutive([$this->row1], [$this->row2], [$this->row3]);

        $this->loader = $this->createMock('Wizaplace\Etl\Loaders\Loader');
        $this->loader->expects($this->any())->method('load')->withConsecutive([$this->row1], [$this->row2], [$this->row3]);

        $this->pipeline = new Pipeline();
        $this->pipeline->extractor($this->extractor);
    }

    /** @test */
    public function pipeline_flow()
    {
        $this->row1->expects($this->once())->method('toArray');
        $this->row2->expects($this->once())->method('toArray');
        $this->row3->expects($this->once())->method('toArray');

        $this->extractor->expects($this->once())->method('extract');
        $this->extractor->expects($this->once())->method('initialize');
        $this->extractor->expects($this->once())->method('finalize');

        $this->transformer->expects($this->exactly(3))->method('transform');
        $this->transformer->expects($this->once())->method('initialize');
        $this->transformer->expects($this->once())->method('finalize');

        $this->loader->expects($this->exactly(3))->method('load');
        $this->loader->expects($this->once())->method('initialize');
        $this->loader->expects($this->once())->method('finalize');

        $this->pipeline->pipe($this->transformer);
        $this->pipeline->pipe($this->loader);

        static::assertEquals([['row1'], ['row2'], ['row3']], iterator_to_array($this->pipeline));
    }

    /** @test */
    public function limit_the_number_of_rows()
    {
        $this->pipeline->limit(1);

        static::assertEquals([['row1']], iterator_to_array($this->pipeline));
    }

    /** @test */
    public function skip_initial_rows()
    {
        $this->pipeline->skip(2);

        static::assertEquals([['row3']], iterator_to_array($this->pipeline));

        $this->pipeline->skip(3);

        static::assertEquals([], iterator_to_array($this->pipeline));
    }

    /** @test */
    public function limit_after_skipping_rows()
    {
        $this->pipeline->skip(1);
        $this->pipeline->limit(1);

        static::assertEquals([['row2']], iterator_to_array($this->pipeline));
    }

    /** @test */
    public function discard_rows()
    {
        $this->row2->expects($this->once())->method('discarded')->willReturn(true);

        $this->pipeline->pipe($this->transformer);
        $this->pipeline->pipe($this->loader);

        $this->transformer->expects($this->exactly(2))->method('transform');
        $this->loader->expects($this->exactly(2))->method('load');

        static::assertEquals([['row1'], ['row3']], iterator_to_array($this->pipeline));
    }
}

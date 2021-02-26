<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\MockObject\MockObject;
use Wizaplace\Etl\Extractors\Extractor;
use Wizaplace\Etl\Loaders\Loader;
use Wizaplace\Etl\Pipeline;
use Wizaplace\Etl\Row;
use Wizaplace\Etl\Transformers\Transformer;

class PipelineTest extends TestCase
{
    /** @var MockObject|Row */
    private $row1;

    /** @var MockObject|Row */
    private $row2;

    /** @var MockObject|Row */
    private $row3;

    /** @var MockObject|Extractor */
    private $extractor;

    /** @var MockObject|Transformer */
    private $transformer;

    /** @var MockObject|Loader */
    private $loader;

    private Pipeline $pipeline;

    protected function setUp(): void
    {
        parent::setUp();

        $this->row1 = $this->createMock('Wizaplace\Etl\Row');
        $this->row1->expects(static::any())->method('toArray')->willReturn(['row1']);

        $this->row2 = $this->createMock('Wizaplace\Etl\Row');
        $this->row2->expects(static::any())->method('toArray')->willReturn(['row2']);

        $this->row3 = $this->createMock('Wizaplace\Etl\Row');
        $this->row3->expects(static::any())->method('toArray')->willReturn(['row3']);

        $generator = function (): \Generator {
            yield $this->row1;
            yield $this->row2;
            yield $this->row3;
        };

        $this->extractor = $this->createMock('Wizaplace\Etl\Extractors\Extractor');
        $this->extractor->expects(static::any())->method('extract')->willReturn($generator());

        $this->transformer = $this->createMock('Wizaplace\Etl\Transformers\Transformer');
        $this->transformer->expects(static::any())->method('transform')
            ->withConsecutive([$this->row1], [$this->row2], [$this->row3]);

        $this->loader = $this->createMock('Wizaplace\Etl\Loaders\Loader');
        $this->loader->expects(static::any())->method('load')
            ->withConsecutive([$this->row1], [$this->row2], [$this->row3]);

        $this->pipeline = new Pipeline();
        $this->pipeline->extractor($this->extractor);
    }

    /** @test */
    public function pipelineFlow(): void
    {
        $this->row1->expects(static::once())->method('toArray');
        $this->row2->expects(static::once())->method('toArray');
        $this->row3->expects(static::once())->method('toArray');

        $this->extractor->expects(static::once())->method('extract');
        $this->extractor->expects(static::once())->method('initialize');
        $this->extractor->expects(static::once())->method('finalize');

        $this->transformer->expects(static::exactly(3))->method('transform');
        $this->transformer->expects(static::once())->method('initialize');
        $this->transformer->expects(static::once())->method('finalize');

        $this->loader->expects(static::exactly(3))->method('load');
        $this->loader->expects(static::once())->method('initialize');
        $this->loader->expects(static::once())->method('finalize');

        $this->pipeline->pipe($this->transformer);
        $this->pipeline->pipe($this->loader);

        static::assertEquals(
            [['row1'], ['row2'], ['row3']],
            $this->pipelineToArray($this->pipeline)
        );
    }

    /** @test */
    public function limitNumberOfRows(): void
    {
        $this->pipeline->limit(1);

        static::assertEquals([['row1']], $this->pipelineToArray($this->pipeline));
    }

    /** @test */
    public function skipInitialRows(): void
    {
        $this->pipeline->skip(2);

        static::assertEquals([['row3']], $this->pipelineToArray($this->pipeline));

        $this->pipeline->skip(3);

        static::assertEquals([], $this->pipelineToArray($this->pipeline));
    }

    /** @test */
    public function limitAfterSkippingRows(): void
    {
        $this->pipeline->skip(1);
        $this->pipeline->limit(1);

        static::assertEquals([['row2']], $this->pipelineToArray($this->pipeline));
    }

    /** @test */
    public function discardRows(): void
    {
        $this->row2->expects(static::once())->method('discarded')->willReturn(true);

        $this->pipeline->pipe($this->transformer);
        $this->pipeline->pipe($this->loader);

        $this->transformer->expects(static::exactly(2))->method('transform');
        $this->loader->expects(static::exactly(2))->method('load');

        static::assertEquals([['row1'], ['row3']], $this->pipelineToArray($this->pipeline));
    }

    protected function pipelineToArray(Pipeline $pipeline): array
    {
        return array_map(
            function (Row $row): array {
                return $row->toArray();
            },
            iterator_to_array($pipeline)
        );
    }
}

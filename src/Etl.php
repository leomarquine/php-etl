<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Wizaplace\Etl;

use Wizaplace\Etl\Extractors\Extractor;
use Wizaplace\Etl\Loaders\Loader;
use Wizaplace\Etl\Transformers\Transformer;

class Etl
{
    /**
     * The etl pipeline.
     *
     * @var \Wizaplace\Etl\Pipeline
     */
    protected $pipeline;

    /**
     * Create a new Etl instance.
     *
     * @param Pipeline $pipeline
     *
     * @return void
     */
    public function __construct(Pipeline $pipeline = null)
    {
        $this->pipeline = $pipeline ?? new Pipeline();
    }

    /**
     * Extract.
     *
     * $input cannot be strictly typed
     * Etl\Extractor\Csv needs a string
     * Etl\Extractor\Collection an \Iterator
     *
     * @param mixed $input
     * @param array $options
     *
     * @return $this
     */
    public function extract(Extractor $extractor, $input, $options = []): Etl
    {
        $extractor->input($input)->options($options);

        $this->pipeline->extractor($extractor);

        return $this;
    }

    /**
     * Transform.
     *
     * @param array $options
     *
     * @return $this
     */
    public function transform(Transformer $transformer, $options = []): Etl
    {
        $transformer->options($options);

        $this->pipeline->pipe($transformer);

        return $this;
    }

    /**
     * Load.
     *
     * @param string $output
     * @param array  $options
     *
     * @return $this
     */
    public function load(Loader $loader, $output, $options = []): Etl
    {
        $loader->output($output)->options($options);

        $this->pipeline->pipe($loader);

        return $this;
    }

    /**
     * Execute the ETL.
     */
    public function run(): void
    {
        $this->pipeline->rewind();

        while ($this->pipeline->valid()) {
            $this->pipeline->next();
        }
    }

    /**
     * Get an array of the resulting ETL data.
     */
    public function toArray(): array
    {
        return iterator_to_array(
            $this->toIterator()
        );
    }

    /**
     * Consume the pipeline as a Generator.
     *
     * @return \Generator<array>
     */
    public function toIterator(): \Generator
    {
        /** @var Row $row */
        foreach ($this->pipeline as $row) {
            if (!$row->discarded()) {
                yield $row->toArray();
            }
        }
    }

    /**
     * Handle dynamic method calls.
     *
     * @param string $method
     * @param array  $parameters
     */
    public function __call($method, $parameters): Etl
    {
        $this->pipeline->$method(...$parameters);

        return $this;
    }
}

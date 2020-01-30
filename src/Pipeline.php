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

class Pipeline implements \Iterator
{
    /**
     * The pipeline data flow.
     *
     * @var \Generator<Row>
     */
    protected $flow;

    /**
     * The maximum number of rows.
     *
     * @var int
     */
    protected $limit;

    /**
     * The number of rows to skip.
     *
     * @var int
     */
    protected $skip;

    /**
     * The iteration key.
     *
     * @var int
     */
    protected $key;

    /**
     * The current iteration row.
     *
     * @var \Wizaplace\Etl\Row
     */
    protected $current;

    /**
     * The etl extractor.
     *
     * @var \Wizaplace\Etl\Extractors\Extractor
     */
    protected $extractor;

    /**
     * The array of steps for the pipeline.
     *
     * @var Step[]
     */
    protected $steps = [];

    /**
     * Set the pipeline extractor.
     */
    public function extractor(Extractor $extractor): void
    {
        $this->extractor = $extractor;
    }

    /**
     * Add a step to the pipeline.
     *
     * @param \Wizaplace\Etl\Step $step
     */
    public function pipe(Step $step): void
    {
        $this->steps[] = $step;
    }

    /**
     * Set the row limit.
     */
    public function limit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * Set the number of rows to skip.
     */
    public function skip(int $skip): void
    {
        $this->skip = $skip;
    }

    /**
     * Get the current element.
     *
     * @return string[]|int[]
     */
    public function current(): array
    {
        return $this->current->toArray();
    }

    /**
     * Move forward to next element.
     */
    public function next(): void
    {
        $this->key++;
        $this->flow->next();
    }

    /**
     * Get the key of the current element.
     */
    public function key(): int
    {
        return $this->key;
    }

    /**
     * Checks if current position is valid.
     */
    public function valid(): bool
    {
        if (false === $this->flow->valid() || true === $this->limitReached()) {
            $this->finalize();

            return false;
        }

        $this->current = $this->flow->current();

        foreach ($this->steps as $step) {
            if ($this->current->discarded()) {
                $this->key--;
                $this->next();

                return $this->valid();
            }

            if ($step instanceof Transformer) {
                $step->transform($this->current);
            }

            if ($step instanceof Loader) {
                $step->load($this->current);
            }
        }

        return true;
    }

    /**
     * Rewind the Iterator to the first element.
     */
    public function rewind(): void
    {
        $this->initialize();

        $this->key = 0;
        $this->flow = $this->extractor->extract();

        while ($this->flow->key() < $this->skip && $this->flow->valid()) {
            $this->flow->next();
        }
    }

    /**
     * Check if the row limit was reached.
     */
    protected function limitReached(): bool
    {
        return $this->limit && $this->key() === $this->limit;
    }

    /**
     * Initialize the steps.
     */
    protected function initialize(): void
    {
        $this->extractor->initialize();

        foreach ($this->steps as $step) {
            $step->initialize();
        }
    }

    /**
     * Finalize the steps.
     */
    protected function finalize(): void
    {
        $this->extractor->finalize();

        foreach ($this->steps as $step) {
            $step->finalize();
        }
    }
}

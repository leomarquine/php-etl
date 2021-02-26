<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl;

use Wizaplace\Etl\Extractors\Extractor;
use Wizaplace\Etl\Loaders\Loader;
use Wizaplace\Etl\Transformers\Transformer;

class Pipeline implements \Iterator
{
    /**
     * The pipeline data flow.
     */
    protected \Generator $flow;

    /**
     * The maximum number of rows.
     */
    protected ?int $limit = null;

    /**
     * The number of rows to skip.
     */
    protected int $skip = 0;

    /**
     * The iteration key.
     */
    protected int $key;

    /**
     * The current iteration row.
     */
    protected Row $current;

    /**
     * The etl extractor.
     */
    protected Extractor $extractor;

    /**
     * The array of steps for the pipeline.
     *
     * @var Step[]
     */
    protected array $steps = [];

    /**
     * Set the pipeline extractor.
     */
    public function extractor(Extractor $extractor): void
    {
        $this->extractor = $extractor;
    }

    /**
     * Add a step to the pipeline.
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
     */
    public function current(): Row
    {
        return $this->current;
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
        return is_int($this->limit) && $this->key() === $this->limit;
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

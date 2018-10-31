<?php

namespace Marquine\Etl;

use Iterator;
use Marquine\Etl\Loaders\Loader;
use Marquine\Etl\Extractors\Extractor;
use Marquine\Etl\Transformers\Transformer;

class Pipeline implements Iterator
{
    /**
     * The pipeline data flow.
     *
     * @var \Generator
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
     * @var \Marquine\Etl\Row
     */
    protected $current;

    /**
     * The etl extractor.
     *
     * @var \Marquine\Etl\Extractors\Extractor
     */
    protected $extractor;

    /**
     * The array of steps for the pipeline.
     *
     * @var array
     */
    protected $steps = [];

    /**
     * Set the pipeline extractor.
     *
     * @param  \Marquine\Etl\Extractors\Extractor  $extractor
     * @return void
     */
    public function extractor(Extractor $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * Add a step to the pipeline.
     *
     * @param  \Marquine\EtlStep  $step
     * @return void
     */
    public function pipe(Step $step)
    {
        $this->steps[] = $step;
    }

    /**
     * Set the row limit.
     *
     * @param  int  $limit
     * @return void
     */
    public function limit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * Set the number of rows to skip.
     *
     * @param  int  $skip
     * @return void
     */
    public function skip($skip)
    {
        $this->skip = $skip;
    }

    /**
     * Get the current element.
     *
     * @return void
     */
    public function current()
    {
        return $this->current->toArray();
    }

    /**
     * Move forward to next element.
     *
     * @return void
     */
    public function next()
    {
        $this->key++;
        $this->flow->next();
    }

    /**
     * Get the key of the current element.
     *
     * @return int
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * Checks if current position is valid.
     *
     * @return bool
     */
    public function valid()
    {
        if (! $this->flow->valid() || $this->limitReached()) {
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
     *
     * @return void
     */
    public function rewind()
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
     *
     * @return bool
     */
    protected function limitReached()
    {
        return $this->limit && $this->key() === $this->limit;
    }

    /**
     * Initialize the steps.
     *
     * @return void
     */
    protected function initialize()
    {
        $this->extractor->initialize();

        foreach ($this->steps as $step) {
            $step->initialize();
        }
    }

    /**
     * Finalize the steps.
     *
     * @return void
     */
    protected function finalize()
    {
        $this->extractor->finalize();

        foreach ($this->steps as $step) {
            $step->finalize();
        }
    }
}

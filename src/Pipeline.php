<?php

namespace Marquine\Etl;

use IteratorAggregate;

class Pipeline
{
    /**
     * The pipeline data flow.
     *
     * @var \IteratorAggregate
     */
    protected $flow;

    /**
     * The array of tasks.
     *
     * @var array
     */
    protected $tasks = [];

    /**
     * Current row.
     *
     * @var int
     */
    protected $current = 0;

    /**
     * Total rows.
     *
     * @var int
     */
    protected $total = 0;

    /**
     * Maximum number of rows.
     *
     * @var int
     */
    protected $limit;

    /**
     * Number of rows to skip.
     *
     * @var int
     */
    protected $skip;

    /**
     * Pre execution callbacks.
     *
     * @var array
     */
    protected $preExecutionCallbacks = [];

    /**
     * Post execution callbacks.
     *
     * @var array
     */
    protected $postExecutionCallbacks = [];

    /**
     * Set the pipeline flow.
     *
     * @param  \IteratorAggregate  $flow
     * @return void
     */
    public function flow(IteratorAggregate $flow)
    {
        $this->flow = $flow;
    }

    /**
     * Pipe a task.
     *
     * @param  callable  $task
     * @return $this
     */
    public function pipe(callable $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Get the pipeline data generator.
     *
     * @return \Generator
     */
    public function get()
    {
        $this->total = $this->getRowsCount();

        foreach ($this->preExecutionCallbacks as $callback) {
            $callback();
        }

        foreach ($this->flow as $index => $row) {
            if ($this->skip && $index < $this->skip) {
                continue;
            }

            if ($this->limit && $this->current == $this->limit) {
                break;
            }

            $this->current++;

            $row = $this->runTasks($row);

            if (!empty($row)) {
                yield $row;
            }
        }

        foreach ($this->postExecutionCallbacks as $callback) {
            $callback();
        }
    }

    /**
     * Get a sample row of the flow.
     *
     * @return array
     */
    public function sample()
    {
        return $this->flow->getIterator()->current();
    }

    /**
     * Run tasks for the given row.
     *
     * @param  array  $row
     * @return array
     */
    protected function runTasks($row)
    {
        foreach ($this->tasks as $task) {
            $row = $task($row);
        }

        return $row;
    }

    /**
     * Get the total rows count.
     *
     * @return int
     */
    protected function getRowsCount()
    {
        $count = iterator_count($this->flow);

        if ($this->skip) {
            $count -= $this->skip;
        }

        if ($this->limit && $count > $this->limit) {
            $count = $this->limit;
        }

        return $count;
    }

    /**
     * Get the metadata.
     *
     * @return array
     */
    public function metadata($attribute = null)
    {
        $metadata = [
            'current' => $this->current,
            'total' => $this->total,
        ];

        return $metadata[$attribute] ?? (object) $metadata;
    }

    /**
     * Set the maximum number of rows.
     *
     * @param  int  $value
     * @return $this
     */
    public function limit($value)
    {
        $this->limit = $value;

        return $this;
    }

    /**
     * Set the number of rows to skip.
     *
     * @param  int  $value
     * @return $this
     */
    public function skip($value)
    {
        $this->skip = $value;

        return $this;
    }

    /**
     * Register pre execution callback.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function before(callable $callback)
    {
        $this->preExecutionCallbacks[] = $callback;

        return $this;
    }

    /**
     * Register post execution callback.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function after(callable $callback)
    {
        $this->postExecutionCallbacks[] = $callback;

        return $this;
    }
}

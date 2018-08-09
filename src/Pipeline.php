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
     * Pre execution tasks.
     *
     * @var array
     */
    protected $preExecutionTasks = [];

    /**
     * Post execution tasks.
     *
     * @var array
     */
    protected $postExecutionTasks = [];

    /**
     * Make a new Pipeline instance.
     *
     * @param  \IteratorAggregate  $flow
     * @return void
     */
    public function __construct(IteratorAggregate $flow)
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
        $this->total = iterator_count($this->flow);

        foreach ($this->preExecutionTasks as $callback) {
            $callback();
        }

        foreach ($this->flow as $row) {
            $this->current++;

            if ($this->skip && $this->current <= $this->skip) {
                continue;
            }

            if ($this->limit && $this->current > $this->limit) {
                break;
            }

            yield $this->runTasks($row);
        }

        foreach ($this->postExecutionTasks as $callback) {
            $callback();
        }
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
            $row = $task($row, $this->metadata());
        }

        return $row;
    }

    /**
     * Get the metadata.
     *
     * @return array
     */
    protected function metadata()
    {
        return [
            'current' => $this->current,
            'total' => $this->total,
        ];
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
     * Register pre execution tasks.
     *
     * @param  callable  $task
     * @return $this
     */
    public function registerPreExecutionTask(callable $task)
    {
        $this->preExecutionTasks[] = $task;

        return $this;
    }

    /**
     * Register post execution tasks.
     *
     * @param  callable  $task
     * @return $this
     */
    public function registerPostExecutionTask(callable $task)
    {
        $this->postExecutionTasks[] = $task;

        return $this;
    }
}

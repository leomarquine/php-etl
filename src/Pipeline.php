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
        foreach ($this->flow as $row) {
            yield $this->runTasks($row);
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
            $row = $task($row);
        }

        return $row;
    }
}

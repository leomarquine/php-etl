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

        foreach ($this->flow as $row) {
            $this->current++;

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
}

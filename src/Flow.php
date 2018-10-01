<?php

namespace Marquine\Etl;

use IteratorAggregate;

class Flow implements IteratorAggregate
{
    /**
     * The iterable data collection.
     *
     * @var iterable
     */
    protected $iterable;

    /**
     * Create a new Flow instance.
     *
     * @param  iterable  $iterable
     * @return void
     */
    public function __construct(iterable $iterable)
    {
        $this->iterable = $iterable;
    }

    /**
     * Get the flow generator.
     *
     * @return \Generator
     */
    public function getIterator()
    {
        foreach($this->iterable as $value) {
            yield $value;
        }
    }
}

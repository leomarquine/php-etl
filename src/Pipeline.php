<?php

namespace Marquine\Etl;

use Generator;

class Pipeline
{
    /**
     * The pipeline data generator.
     *
     * @var \Generator
     */
    protected $generator;

    /**
     * The array of transformers.
     *
     * @var array
     */
    protected $transformers = [];

    /**
     * Make a new Pipeline instance.
     *
     * @param  \Generator  $generator
     * @return void
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Pipe a transformer.
     *
     * @param  callable  $transformer
     * @return $this
     */
    public function pipe(callable $transformer)
    {
        $this->transformers[] = $transformer;

        return $this;
    }

    /**
     * Get the pipeline data generator.
     *
     * @return \Generator
     */
    public function get()
    {
        foreach ($this->generator as $row) {
            yield $this->transform($row);
        }
    }

    /**
     * Transform the row data.
     *
     * @param  array  $row
     * @return array
     */
    protected function transform($row)
    {
        foreach ($this->transformers as $transformer) {
            $row = $transformer($row);
        }

        return $row;
    }
}

<?php

namespace Marquine\Etl\Extractors;

use Marquine\Etl\Etl;
use Marquine\Etl\Pipeline;
use Marquine\Etl\Exceptions\FileNotFoundException;

abstract class Extractor
{
    /**
     * Extract data from the given source.
     *
     * @param  string  $source
     * @return \Generator
     */
    abstract public function extract($source);

    /**
     * Get the extractor pipeline.
     *
     * @param  string  $source
     * @return \Marquine\Etl\Pipeline
     */
    public function pipeline($source)
    {
        return new Pipeline($this->extract($source));
    }

    /**
     * Validate the given source file.
     *
     * @param  string  $source
     * @return string
     *
     * @throws \Marquine\Etl\Exceptions\FileNotFoundException
     */
    protected function validateSourceFile($source)
    {
        $name = $source;

        if (filter_var($source, FILTER_VALIDATE_URL) || is_file($source)) {
            return $source;
        }

        if (is_file($source = Etl::get('path').DIRECTORY_SEPARATOR.$source)) {
            return $source;
        }

        throw new FileNotFoundException("The file '$name' was not found.");
    }
};

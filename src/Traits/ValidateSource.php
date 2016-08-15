<?php

namespace Marquine\Etl\Traits;

use Marquine\Etl\Etl;

trait ValidateSource
{
    /**
     * Validate the given source.
     *
     * @param string $source
     * @return string
     */
    protected function validateSource($source)
    {
        if (filter_var($source, FILTER_VALIDATE_URL) || is_file($source)) {
            return $source;
        }

        return Etl::config('default_path') . DIRECTORY_SEPARATOR . $source;
    }
}

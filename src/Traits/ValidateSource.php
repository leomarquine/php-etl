<?php

namespace Marquine\Metis\Traits;

use Marquine\Metis\Metis;

trait ValidateSource
{
    /**
     * Validate the given source.
     *
     * @param  string $source
     * @return string
     */
    protected function validateSource($source)
    {
        if (filter_var($source, FILTER_VALIDATE_URL) || is_file($source)) {
            return $source;
        }

        return Metis::config('default_path') . DIRECTORY_SEPARATOR . $source;
    }
}

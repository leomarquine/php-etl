<?php

namespace Marquine\Metis\Extractors;

use Marquine\Metis\Contracts\Extractor;

class ArrayData implements Extractor
{
    /**
     * Extract data from the given source.
     *
     * @param  array $source
     * @param  mixed $columns
     * @return array
     */
    public function extract($source, $columns)
    {
        if ($columns) {
            $columns = array_flip($columns);

            foreach ($source as &$row) {
                $row = array_intersect_key($row, $columns);
            }
        }

        return $source;
    }
}

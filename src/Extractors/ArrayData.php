<?php

namespace Marquine\Etl\Extractors;

class ArrayData implements ExtractorInterface
{
    /**
     * Extractor columns.
     *
     * @var array
     */
    public $columns;

    /**
     * Extract data from the given source.
     *
     * @param array $source
     * @return array
     */
    public function extract($source)
    {
        if ($this->columns) {
            $this->columns = array_flip($this->columns);

            foreach ($source as &$row) {
                $row = array_intersect_key($row, $this->columns);
            }
        }

        return $source;
    }
}

<?php

namespace Marquine\Etl\Extractors;

use SimpleXMLElement;
use Marquine\Etl\Traits\ValidateSource;

class Xml implements ExtractorInterface
{
    use ValidateSource;

    /**
     * Extractor columns.
     *
     * @var array
     */
    public $columns;

    /**
     * The loop path.
     *
     * @var string
     */
    public $loop = '/';

    /**
     * Extract data from the given source.
     *
     * @param string $source
     * @return array
     */
    public function extract($source)
    {
        $source = $this->validateSource($source);

        $xml = new SimpleXMLElement(file_get_contents($source));

        $elements = $xml->xpath($this->loop);

        $items = [];

        foreach ($elements as $key => $row) {
            $items[] = $this->makeRow($this->columns, $row);
        }

        return $items;
    }

    /**
     * Make a row using custom column paths.
     *
     * @param array $columns
     * @param SimpleXMLElement $row
     * @return array
     */
    protected function makeRow($columns, $row)
    {
        if (! $columns) {
            return $this->parse($row);
        }

        $data = [];

        foreach ($columns as $column => $path) {
            $value = $row->xpath($path);
            $data[$column] = (string) array_shift($value);
        }

        return $data;
    }

    /**
     * Convert a SimpleXMLElement to array.
     *
     * @param SimpleXMLElement $row
     * @return array
     */
    protected function parse($row)
    {
        return json_decode(json_encode($row), true);
    }
}

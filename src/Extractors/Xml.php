<?php

namespace Marquine\Etl\Extractors;

use SimpleXMLElement;
use Marquine\Etl\Support\ValidateSource;

class Xml extends Extractor
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
     * @param  string  $source
     * @return \Generator
     */
    public function extract($source)
    {
        $source = $this->validateSource($source);

        $xml = new SimpleXMLElement(file_get_contents($source));

        $elements = $xml->xpath($this->loop);

        foreach ($elements as $key => $row) {
            yield $this->makeRow($row);
        }
    }

    /**
     * Make a row using custom column paths.
     *
     * @param  SimpleXMLElement  $row
     * @return array
     */
    protected function makeRow($row)
    {
        if (! $this->columns) {
            return $this->parse($row);
        }

        $data = [];

        foreach ($this->columns as $column => $path) {
            $value = $row->xpath($path);
            $data[$column] = (string) array_shift($value);
        }

        return $data;
    }

    /**
     * Convert a SimpleXMLElement to array.
     *
     * @param  SimpleXMLElement  $row
     * @return array
     */
    protected function parse($row)
    {
        return json_decode(json_encode($row), true);
    }
}

<?php

namespace Marquine\Metis\Extractors;

use SimpleXMLElement;
use Marquine\Metis\Traits\SetOptions;
use Marquine\Metis\Contracts\Extractor;
use Marquine\Metis\Traits\ValidateSource;

class Xml implements Extractor
{
    use SetOptions, ValidateSource;

    /**
     * The loop path.
     *
     * @var string
     */
    protected $loop = '/';

    /**
     * Extract data from the given source.
     *
     * @param  string $source
     * @param  mixed  $columns
     * @return array
     */
    public function extract($source, $columns)
    {
        $source = $this->validateSource($source);

        $xml = new SimpleXMLElement(file_get_contents($source));

        $elements = $xml->xpath($this->loop);

        $items = [];

        foreach ($elements as $key => $row) {
            $items[] = $this->makeRow($columns, $row);
        }

        return $items;
    }

    /**
     * Make a row using custom column paths.
     *
     * @param  array $columns
     * @param  SimpleXMLElement $row
     * @return array
     */
    protected function makeRow($columns, $row)
    {
        if (! $columns) {
            return $this->parse($row);
        }

        $data = [];

        foreach ($columns as $column => $path) {
            $data[$column] = (string) array_shift($row->xpath($path));
        }

        return $data;
    }

    /**
     * Convert a SimpleXMLElement to array.
     *
     * @param  SimpleXMLElement $row
     * @return array
     */
    protected function parse($row)
    {
        return json_decode(json_encode($row), true);
    }
}

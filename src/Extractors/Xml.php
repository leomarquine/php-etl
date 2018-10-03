<?php

namespace Marquine\Etl\Extractors;

use XMLReader;

class Xml extends Extractor
{
    /**
     * Extractor columns.
     *
     * @var array
     */
    protected $columns;

    /**
     * The loop path.
     *
     * @var string
     */
    protected $loop = '/';

    /**
     * XML Reader.
     *
     * @var \XMLReader
     */
    protected $reader;

    /**
     * The current xml path
     *
     * @var string
     */
    protected $path;

    /**
     * Current row.
     *
     * @var array
     */
    protected $row = [];

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns', 'loop'
    ];

    /**
     * Extract data from the given source.
     *
     * @param  mixed  $source
     * @return iterable
     */
    public function extract($source)
    {
        if ($this->columns) {
            foreach ($this->columns as &$value) {
                $value = $this->loop . $value;
            }
        }

        $this->reader = new XMLReader;

        $this->reader->open($source);

        while ($this->reader->read()) {
            $this->addElementToPath();
            $this->handleNodeValue();
            $this->handleNodeAttributes();
            $this->removeElementFromPath();

            if ($this->isCycleComplete()) {
                yield $this->getCurrentRow();
            }
        }

        $this->reader->close();
    }

    /**
     * Get the current row.
     *
     * @return array
     */
    protected function getCurrentRow()
    {
        $row = $this->row;

        $this->row = [];

        return $row;
    }

    /**
     * Check if the current node is an element.
     *
     * @return bool
     */
    protected function isElement()
    {
        return $this->reader->nodeType == XMLReader::ELEMENT;
    }

    /**
     * Check if the current node is an end element.
     *
     * @return bool
     */
    protected function isEndElement()
    {
        return $this->reader->nodeType == XMLReader::END_ELEMENT || $this->reader->isEmptyElement;
    }

    /**
     * Check if the current node is a value.
     *
     * @return bool
     */
    protected function isValue()
    {
        return $this->reader->nodeType == XMLReader::TEXT || $this->reader->nodeType === XMLReader::CDATA || $this->reader->nodeType === XMLReader::ATTRIBUTE;
    }

    /**
     * Check if the current node has attributes.
     *
     * @return bool
     */
    protected function hasAttributes()
    {
        return $this->reader->nodeType == XMLReader::ELEMENT && $this->reader->hasAttributes;
    }

    /**
     * Check if the iteration cycle is complete based on the loop path.
     *
     * @return bool
     */
    protected function isCycleComplete()
    {
        $value = $this->path . '/' . $this->reader->name;
        $pattern = $this->loop;

        if ($value == $pattern) {
            return $this->reader->nodeType == XMLReader::END_ELEMENT;
        }

        $pattern = preg_quote($pattern, '#');

        $pattern = str_replace('\*', '.*', $pattern);

        if (preg_match('#^' . $pattern . '\z#u', $value) === 1) {
            return $this->reader->nodeType == XMLReader::END_ELEMENT;
        }

        return false;
    }

    /**
     * Add the current element to the xml path.
     *
     * @return void
     */
    protected function addElementToPath()
    {
        if ($this->isElement()) {
            $this->path = $this->path . '/' . $this->reader->name;
        }
    }

    /**
     * Remove the current element from the xml path.
     *
     * @return void
     */
    protected function removeElementFromPath()
    {
        if ($this->isEndElement()) {
            $this->path = substr($this->path, 0, strrpos($this->path, '/'));
        }
    }

    /**
     * Add the current attribute to the xml path.
     *
     * @return void
     */
    protected function addAttributeToPath()
    {
        $this->path = $this->path . '/@' . $this->reader->name;
    }

    /**
     * Remove the current attribute from the xml path.
     *
     * @return void
     */
    protected function removeAttributeFromPath()
    {
        $this->path = substr($this->path, 0, strrpos($this->path, '/'));
    }

    /**
     * Handle current node attributes.
     *
     * @return void
     */
    protected function handleNodeAttributes()
    {
        if (! $this->hasAttributes()) {
            return;
        }

        while ($this->reader->moveToNextAttribute()) {
            $this->addAttributeToPath();
            $this->handleNodeValue();
            $this->removeAttributeFromPath();
        }

        $this->reader->moveToElement();
    }

    /**
     * Handle the current node value.
     *
     * @return void
     */
    protected function handleNodeValue()
    {
        if (! $this->isValue()) {
            return;
        }

        if (empty($this->columns)) {
            $column = ltrim(strrchr($this->path, '/'), '/@');
        }

        if (in_array($this->path, (array) $this->columns)) {
            $column = array_search($this->path, $this->columns);
        }

        if (isset($column)) {
            $this->row[$column] = $this->reader->value;
        }
    }
}

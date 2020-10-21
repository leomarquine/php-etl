<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Extractors;

use Wizaplace\Etl\Row;

class Xml extends Extractor
{
    /**
     * Extractor columns.
     *
     * @var array|null
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
     * @var \XMLReader|bool
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
        'columns', 'loop',
    ];

    /**
     * Extract data from the input.
     */
    public function extract(): \Generator
    {
        if (is_array($this->columns) && [] !== $this->columns) {
            foreach ($this->columns as &$value) {
                $value = $this->loop . $value;
            }
        }

        $this->reader = \XMLReader::open($this->input);
        if (false === $this->reader) {
            throw new XmlException('Could not open XMLReader.');
        }

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
     */
    protected function getCurrentRow(): Row
    {
        $row = $this->row;

        $this->row = [];

        return new Row($row);
    }

    /**
     * Check if the current node is an element.
     */
    protected function isElement(): bool
    {
        return \XMLReader::ELEMENT == $this->reader->nodeType;
    }

    /**
     * Check if the current node is an end element.
     */
    protected function isEndElement(): bool
    {
        return \XMLReader::END_ELEMENT == $this->reader->nodeType || $this->reader->isEmptyElement;
    }

    /**
     * Check if the current node is a value.
     */
    protected function isValue(): bool
    {
        return \XMLReader::TEXT == $this->reader->nodeType
            || \XMLReader::CDATA === $this->reader->nodeType
            || \XMLReader::ATTRIBUTE === $this->reader->nodeType;
    }

    /**
     * Check if the current node has attributes.
     */
    protected function hasAttributes(): bool
    {
        return \XMLReader::ELEMENT == $this->reader->nodeType && $this->reader->hasAttributes;
    }

    /**
     * Check if the iteration cycle is complete based on the loop path.
     */
    protected function isCycleComplete(): bool
    {
        $value = $this->path . '/' . $this->reader->name;
        $pattern = $this->loop;

        if ($value == $pattern) {
            return \XMLReader::END_ELEMENT == $this->reader->nodeType;
        }

        $pattern = preg_quote($pattern, '#');

        $pattern = str_replace('\*', '.*', $pattern);

        if (1 === preg_match('#^' . $pattern . '\z#u', $value)) {
            return \XMLReader::END_ELEMENT == $this->reader->nodeType;
        }

        return false;
    }

    /**
     * Add the current element to the xml path.
     */
    protected function addElementToPath(): void
    {
        if ($this->isElement()) {
            $this->path = $this->path . '/' . $this->reader->name;
        }
    }

    /**
     * Remove the current element from the xml path.
     */
    protected function removeElementFromPath(): void
    {
        if ($this->isEndElement()) {
            $this->path = substr($this->path, 0, strrpos($this->path, '/'));
        }
    }

    /**
     * Add the current attribute to the xml path.
     */
    protected function addAttributeToPath(): void
    {
        $this->path = $this->path . '/@' . $this->reader->name;
    }

    /**
     * Remove the current attribute from the xml path.
     */
    protected function removeAttributeFromPath(): void
    {
        $this->path = substr($this->path, 0, strrpos($this->path, '/'));
    }

    /**
     * Handle current node attributes.
     */
    protected function handleNodeAttributes(): void
    {
        if (!$this->hasAttributes()) {
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
     */
    protected function handleNodeValue(): void
    {
        if (!$this->isValue()) {
            return;
        }

        if (false === is_array($this->columns) || [] === $this->columns) {
            $column = ltrim(strrchr($this->path, '/'), '/@');
        }

        if (in_array($this->path, (array) $this->columns, true)) {
            $column = array_search($this->path, $this->columns, true);
        }

        if (isset($column)) {
            $this->row[$column] = $this->reader->value;
        }
    }
}

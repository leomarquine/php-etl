<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @author      Karl DeBisschop <kdebisschop@gmail.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Extractors;

use Wizaplace\Etl\Row;

/**
 * Uses a Generator callback to yield multiple rows for each row of an input.
 *
 * In particular, can use $pipeline->extract(...)->toIterator() as input to this Extractor.
 * In such a configuration, two extractors can be chained together to return N x M rows
 * selecting N rows of JSON arrays from an SQL database, each containing M elements in the
 * JSON array.
 *
 *  ```php
 *  $iterable = [
 *      ['id' => 1, 'json' => '["a", "b", "c"]'],
 *      ['id' => 2, 'json' => '["x", "y", "z"]'],
 *  ];
 *
 *  $options[GeneratorCallback::CALLBACK] = function ($row) {
 *      foreach (json_decode($row['json']) as $value) {
 *          yield ['id' => $row['id'], 'value' => $value];
 *      }
 *  };
 *
 *  // @var GeneratorCallback $extractor
 *  $pipeline->extract($extractor, $iterable, $options);
 *
 *  // Alternatively...
 *  // @var Table $tableExtractor
 *  $iterable = $source->extract($tableExtractor, 'tableName', [Table::CONNECTION => 'default'])->toArray();
 *  $pipeline->extract($extractor, $iterable, $options);
 *  ```
 */
class GeneratorCallback extends Extractor
{
    public const CALLBACK = 'callback';

    /**
     * Properties that can be set via the options method.
     *
     * @var string[]
     */
    protected array $availableOptions = [self::CALLBACK];

    /**
     * A callback function that takes a row array as its input and returns an iterable object.
     *
     * @var callable
     */
    protected $callback;

    public function extract(): \Generator
    {
        /** @var Row $row */
        foreach ($this->input as $row) {
            foreach (($this->callback)($row) as $newRow) {
                yield new Row($newRow);
            }
        }
    }
}

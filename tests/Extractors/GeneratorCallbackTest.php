<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @author      Karl DeBisschop <kdebisschop@gmail.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Extractors;

use Tests\TestCase;
use Wizaplace\Etl\Extractors\GeneratorCallback;
use Wizaplace\Etl\Row;

class GeneratorCallbackTest extends TestCase
{
    /** @var array|array[] */
    protected array $input = [
        ['id' => 1, 'json' => '["a", "b", "c"]'],
        ['id' => 2, 'json' => '["x", "y", "z"]'],
    ];

    protected \Closure $callback;

    /**
     * @var array|Row[]
     */
    private array $expected;

    protected function setUp(): void
    {
        parent::setUp();
        $this->expected = [
            new Row(['id' => 1, 'value' => 'a']),
            new Row(['id' => 1, 'value' => 'b']),
            new Row(['id' => 1, 'value' => 'c']),
            new Row(['id' => 2, 'value' => 'x']),
            new Row(['id' => 2, 'value' => 'y']),
            new Row(['id' => 2, 'value' => 'z']),
        ];
        $this->callback = function ($row): \Generator {
            foreach (json_decode($row['json']) as $value) {
                yield ['id' => $row['id'], 'value' => $value];
            }
        };
    }

    /** @test */
    public function withClosure(): void
    {
        $extractor = new GeneratorCallback();
        $extractor->input($this->input);
        $extractor->options(['callback' => $this->callback]);
        static::assertEquals($this->expected, iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function withClassAndMethod(): void
    {
        $extractor = new GeneratorCallback();
        $extractor->input($this->input);
        $extractor->options(['callback' => ['\Tests\Extractors\GeneratorCallbackTest', 'explodeJson']]);
        static::assertEquals($this->expected, iterator_to_array($extractor->extract()));
    }

    /**
     * Test fixture that show using a static method and returning an array.
     *
     * In practice, a generator should almost always preferred to returning an array.
     */
    public static function explodeJson(array $row): array
    {
        $array = [];
        foreach (json_decode($row['json']) as $value) {
            $array[] = ['id' => $row['id'], 'value' => $value];
        }

        return $array;
    }
}

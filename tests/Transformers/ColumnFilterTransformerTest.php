<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Transformers;

use PHPUnit\Framework\TestCase;
use Wizaplace\Etl\Row;
use Wizaplace\Etl\Transformers\ColumnFilterTransformer;

class ColumnFilterTransformerTest extends TestCase
{
    protected ColumnFilterTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transformer = new ColumnFilterTransformer();
    }

    public function testTransformWithColumnName(): void
    {
        $row = new Row(['a' => 'b', 'c' => 'd', 'e' => 'f', 'g' => 'h']);

        $this->transformer->options(
            [
                $this->transformer::COLUMNS => ['c', 'g'],
            ]
        );
        $this->transformer->transform($row);

        static::assertSame(['c' => 'd', 'g' => 'h'], $row->toArray());
    }

    public function testTransformWithCallback(): void
    {
        $row = new Row(['a' => 'keep', 'c' => 'drop', 'e' => 'keep', 'g' => 'special']);

        $this->transformer->options(
            [
                $this->transformer::CALLBACK => function (string $column, string $value): bool {
                    return 'g' === $column || 'keep' === $value;
                },
            ]
        );
        $this->transformer->transform($row);

        static::assertSame(['a' => 'keep', 'e' => 'keep', 'g' => 'special'], $row->toArray());
    }

    public function testTransformWithBothFilters(): void
    {
        $row = new Row(['a' => 'keep', 'c' => 'drop', 'e' => 'keep', 'g' => 'special']);

        $this->transformer->options([
            $this->transformer::COLUMNS => ['c', 'e'],
            $this->transformer::CALLBACK => function (string $column, string $value): bool {
                return 'keep' === $value;
            },
        ]);
        $this->transformer->transform($row);

        static::assertSame(['e' => 'keep'], $row->toArray());
    }

    public function testTransformWithoutFilter(): void
    {
        $row = new Row(['a' => 'keep', 'c' => 'drop', 'e' => 'keep', 'g' => 'special']);
        $expected = clone $row;

        $this->transformer->transform($row);

        static::assertSame($expected->toArray(), $row->toArray());
    }
}

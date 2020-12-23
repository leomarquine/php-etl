<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests;

use Wizaplace\Etl\Row;

class RowTest extends TestCase
{
    /** @test */
    public function setAttribute(): void
    {
        $row = new Row([]);

        $row->set('name', 'Jane Doe');

        static::assertEquals('Jane Doe', $row->get('name'));
    }

    /** @test */
    public function getAttribute(): void
    {
        $row = new Row(['name' => 'Jane Doe']);

        static::assertEquals('Jane Doe', $row->get('name'));
        static::assertNull($row->get('invalid'));
    }

    /** @test */
    public function removeAttribute(): void
    {
        $row = new Row(['name' => 'Jane Doe']);

        $row->remove('name');

        static::assertNull($row->get('name'));
    }

    /** @test */
    public function transformValuesUsingCallback(): void
    {
        $row = new Row(['name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $row->transform([], function (string $value): string {
            return "*$value*";
        });

        static::assertEquals('*Jane Doe*', $row->get('name'));
        static::assertEquals('*janedoe@example.com*', $row->get('email'));

        $row->transform(['name'], function (string $value): string {
            return trim($value, '*');
        });

        static::assertEquals('Jane Doe', $row->get('name'));
        static::assertEquals('*janedoe@example.com*', $row->get('email'));
    }

    /** @test */
    public function getArrayRepresentationOfRow(): void
    {
        $row = new Row(['name' => 'Jane Doe']);

        static::assertEquals(['name' => 'Jane Doe'], $row->toArray());
    }

    /** @test */
    public function discardRow(): void
    {
        $row = new Row([]);

        static::assertFalse($row->discarded());

        $row->discard();

        static::assertTrue($row->discarded());
    }

    /** @test */
    public function arrayAccess(): void
    {
        $row = new Row(['name' => 'Jane Doe']);

        static::assertTrue(isset($row['name']));

        static::assertEquals('Jane Doe', $row['name']);

        $row['name'] = 'John Doe';

        static::assertEquals('John Doe', $row['name']);

        unset($row['name']);

        static::assertFalse(isset($row['name']));
    }

    /** @test */
    public function objectAccess(): void
    {
        $row = new Row(['name' => 'Jane Doe']);

        static::assertEquals('Jane Doe', $row->name);

        $row->name = 'John Doe';

        static::assertEquals('John Doe', $row->name);
    }

    /** @test */
    public function setAttributesWithoutMerge(): void
    {
        $row = new Row(['name' => 'Jane Doe', 'Sex' => 'Female']);
        $newAttributes = ['name' => 'Pocahontas', 'Sex' => 'Female'];
        $row->setAttributes($newAttributes);
        static::assertEquals($newAttributes, $row->toArray());
    }

    /** @test */
    public function setAttributesWithMerge(): void
    {
        $row = new Row(['name' => 'Jane Doe', 'Sex' => 'Female']);
        $newAttributes = ['name' => 'Marie Curie', 'Job' => 'Scientist'];
        $row->setAttributes($newAttributes, true);
        static::assertEquals([
            'name' => 'Marie Curie',
            'Sex' => 'Female',
            'Job' => 'Scientist',
        ], $row->toArray());
    }

    /** @test */
    public function clearAttributes(): void
    {
        $row = new Row(['name' => 'Jane Doe', 'Sex' => 'Female']);
        $row->clearAttributes();
        static::assertEmpty($row->toArray());
    }
}

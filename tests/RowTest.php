<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Tests;

use Wizaplace\Etl\Row;

class RowTest extends TestCase
{
    /** @test */
    public function set_attribute()
    {
        $row = new Row([]);

        $row->set('name', 'Jane Doe');

        static::assertEquals('Jane Doe', $row->get('name'));
    }

    /** @test */
    public function get_attribute()
    {
        $row = new Row(['name' => 'Jane Doe']);

        static::assertEquals('Jane Doe', $row->get('name'));
        static::assertNull($row->get('invalid'));
    }

    /** @test */
    public function remove_attribute()
    {
        $row = new Row(['name' => 'Jane Doe']);

        $row->remove('name');

        static::assertNull($row->get('name'));
    }

    /** @test */
    public function transform_values_using_a_callback()
    {
        $row = new Row(['name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $row->transform([], function ($value) {
            return "*$value*";
        });

        static::assertEquals('*Jane Doe*', $row->get('name'));
        static::assertEquals('*janedoe@example.com*', $row->get('email'));

        $row->transform(['name'], function ($value) {
            return trim($value, '*');
        });

        static::assertEquals('Jane Doe', $row->get('name'));
        static::assertEquals('*janedoe@example.com*', $row->get('email'));
    }

    /** @test */
    public function get_the_array_representation_of_the_row()
    {
        $row = new Row(['name' => 'Jane Doe']);

        static::assertEquals(['name' => 'Jane Doe'], $row->toArray());
    }

    /** @test */
    public function discard_row()
    {
        $row = new Row([]);

        static::assertFalse($row->discarded());

        $row->discard();

        static::assertTrue($row->discarded());
    }

    /** @test */
    public function array_access()
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
    public function object_access()
    {
        $row = new Row(['name' => 'Jane Doe']);

        static::assertEquals('Jane Doe', $row->name);

        $row->name = 'John Doe';

        static::assertEquals('John Doe', $row->name);
    }

    /** @test */
    public function set_attributes_without_merge()
    {
        $row = new Row(['name' => 'Jane Doe', 'Sex' => 'Female']);
        $newAttributes = ['name' => 'Pocahontas', 'Sex' => 'Female'];
        $row->setAttributes($newAttributes);
        static::assertEquals($newAttributes, $row->toArray());
    }

    /** @test */
    public function set_attributes_with_merge()
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
    public function clear_attributes()
    {
        $row = new Row(['name' => 'Jane Doe', 'Sex' => 'Female']);
        $row->clearAttributes();
        static::assertEmpty($row->toArray());
    }
}

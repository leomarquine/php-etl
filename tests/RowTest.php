<?php

namespace Tests;

use Marquine\Etl\Row;

class RowTest extends TestCase
{
    /** @test */
    public function set_attribute()
    {
        $row = new Row([]);

        $row->set('name', 'Jane Doe');

        $this->assertAttributeEquals(['name' => 'Jane Doe'], 'attributes', $row);
    }

    /** @test */
    public function get_attribute()
    {
        $row = new Row(['name' => 'Jane Doe']);

        $this->assertEquals('Jane Doe', $row->get('name'));
        $this->assertNull($row->get('invalid'));
    }

    /** @test */
    public function remove_attribute()
    {
        $row = new Row(['name' => 'Jane Doe']);

        $row->remove('name');

        $this->assertAttributeEquals([], 'attributes', $row);
    }

    /** @test */
    public function transform_values_using_a_callback()
    {
        $row = new Row(['name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $row->transform([], function ($value) {
            return "*$value*";
        });

        $this->assertAttributeEquals(['name' => '*Jane Doe*', 'email' => '*janedoe@example.com*'], 'attributes', $row);

        $row->transform(['name'], function ($value) {
            return trim($value, '*');
        });

        $this->assertAttributeEquals(['name' => 'Jane Doe', 'email' => '*janedoe@example.com*'], 'attributes', $row);
    }

    /** @test */
    public function get_the_array_representation_of_the_row()
    {
        $row = new Row(['name' => 'Jane Doe']);

        $this->assertEquals(['name' => 'Jane Doe'], $row->toArray());
    }

    /** @test */
    public function discard_row()
    {
        $row = new Row([]);

        $this->assertFalse($row->discarded());

        $row->discard();

        $this->assertTrue($row->discarded());
    }

    /** @test */
    public function create_a_hash_of_the_row()
    {
        $row = new Row(['data']);

        $hash = md5(serialize(['data']));

        $this->assertEquals($hash, $row->hash());
    }

    /** @test */
    public function array_access()
    {
        $row = new Row(['name' => 'Jane Doe']);

        $this->assertTrue(isset($row['name']));

        $this->assertEquals('Jane Doe', $row['name']);

        $row['name'] = 'John Doe';

        $this->assertEquals('John Doe', $row['name']);

        unset($row['name']);

        $this->assertFalse(isset($row['name']));
    }

    /** @test */
    public function object_access()
    {
        $row = new Row(['name' => 'Jane Doe']);

        $this->assertEquals('Jane Doe', $row->name);

        $row->name = 'John Doe';

        $this->assertEquals('John Doe', $row->name);
    }
}

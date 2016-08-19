<?php

namespace Tests\Loaders;

use DateTime;
use Tests\TestCase;
use Marquine\Etl\Etl;
use Marquine\Etl\Loaders\Table;

class TableTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->createTables();
    }

    /** @test */
    function insert_data_into_table()
    {
        $items = [
            ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $loader = new Table;

        $loader->load('users', $items);

        $results = Etl::database()->select('users');

        $this->assertEquals($items, $results);
    }

    /** @test */
    function update_table_data()
    {
        Etl::database()->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        Etl::database()->exec("insert into users values (2, 'Jane', 'janedoe@email.com')");

        $items = [
            ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $loader = new Table;

        $loader->load('users', $items);

        $results = Etl::database()->select('users');

        $this->assertEquals($items, $results);
    }

    /** @test */
    function delete_records_that_are_not_in_the_source()
    {
        Etl::database()->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        Etl::database()->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $items = [
            ['id' => '1', 'name' => 'John', 'email' => 'johndoe@email.com'],
        ];

        $loader = new Table;

        $loader->delete = true;

        $loader->load('users', $items);

        $results = Etl::database()->select('users');

        $this->assertEquals($items, $results);
    }

    /** @test */
    function insert_data_into_table_with_timestamps()
    {
        $items = [
            ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $loader = new Table;

        $loader->timestamps = true;

        $loader->load('users_ts', $items);

        $results = Etl::database()->select('users_ts');

        foreach ($results as $row) {
            $this->assertTrue((bool) DateTime::createFromFormat('Y-m-d G:i:s', $row['created_at']));
            $this->assertTrue((bool) DateTime::createFromFormat('Y-m-d G:i:s', $row['updated_at']));
            $this->assertEquals($row['created_at'], $row['updated_at']);
        }
    }

    /** @test */
    function update_table_data_with_timestamps()
    {
        Etl::database()->exec("insert into users_ts (id, name, email) values (1, 'John', 'johndoe@email.com')");
        Etl::database()->exec("insert into users_ts (id, name, email) values (2, 'Jane', 'janedoe@email.com')");

        $items = [
            ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $loader = new Table;

        $loader->timestamps = true;

        $loader->load('users_ts', $items);

        $results = Etl::database()->select('users_ts');

        foreach ($results as $row) {
            $this->assertTrue((bool) DateTime::createFromFormat('Y-m-d G:i:s', $row['updated_at']));
            $this->assertNull($row['created_at']);
        }
    }

    /** @test */
    function soft_delete_records_that_are_not_in_the_source()
    {
        Etl::database()->exec("insert into users_ts (id, name, email) values (1, 'John Doe', 'johndoe@email.com')");
        Etl::database()->exec("insert into users_ts (id, name, email) values (2, 'Jane Doe', 'janedoe@email.com')");

        $items = [];

        $loader = new Table;

        $loader->delete = 'soft';

        $loader->load('users_ts', $items);

        $results = Etl::database()->select('users_ts');

        $this->assertNotEmpty($results);

        foreach ($results as $row) {
            $this->assertTrue((bool) DateTime::createFromFormat('Y-m-d G:i:s', $row['deleted_at']));
        }
    }

    /** @test */
    function load_data_into_table_using_a_custom_connection()
    {
        $this->createTables('secondary');

        $items = [
            ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $loader = new Table;

        $loader->connection = 'secondary';

        $loader->load('users', $items);

        $results = Etl::database('secondary')->select('users');

        $this->assertEquals($items, $results);
    }
}

<?php

namespace Tests\Loaders;

use Tests\TestCase;
use Marquine\Etl\Loaders\Insert;
use Marquine\Etl\Database\Manager as DB;

class InsertTest extends TestCase
{
    /** @test */
    function insert_data_into_the_database()
    {
        $this->createUsersTable('default');

        $data = function () {
            yield ['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com'];
            yield ['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@example.com'];
        };

        $loader = new Insert;

        $loader->load($data(), 'users');

        $expected = [
            ['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com'],
            ['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@example.com'],
        ];

        $query = DB::connection('default')->query()->select('users')->execute();

        $this->assertEquals($expected, $query->fetchAll());
    }

    /** @test */
    function insert_specified_into_the_database()
    {
        $this->createUsersTable('default');

        $data = function () {
            yield ['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com'];
            yield ['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@example.com'];
        };

        $loader = new Insert;

        $loader->columns = ['id', 'name'];

        $loader->load($data(), 'users');


        $expected = [
            ['id' => '1', 'name' => 'Jane Doe', 'email' => ''],
            ['id' => '2', 'name' => 'John Doe', 'email' => ''],
        ];

        $query = DB::connection('default')->query()->select('users')->execute();

        $this->assertEquals($expected, $query->fetchAll());
    }

    /** @test */
    function insert_data_into_the_database_with_timestamps()
    {
        $this->createUsersTable('default', true);

        $data = function () {
            yield ['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com'];
            yield ['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@example.com'];
        };

        $loader = new Insert;

        $loader->timestamps = true;

        $loader->load($data(), 'users');

        $expected = [
            ['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com', 'created_at' => date('Y-m-d G:i:s'), 'updated_at' => date('Y-m-d G:i:s'), 'deleted_at' => null],
            ['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@example.com', 'created_at' => date('Y-m-d G:i:s'), 'updated_at' => date('Y-m-d G:i:s'), 'deleted_at' => null],
        ];

        $query = DB::connection('default')->query()->select('users')->execute();

        $this->assertEquals($expected, $query->fetchAll());
    }
}

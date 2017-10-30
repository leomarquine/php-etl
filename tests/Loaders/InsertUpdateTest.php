<?php

namespace Tests\Loaders;

use Tests\TestCase;
use Marquine\Etl\Loaders\InsertUpdate;
use Marquine\Etl\Database\Manager as DB;

class InsertUpdateTest extends TestCase
{
    /** @test */
    public function insert_and_update_data()
    {
        $this->createUsersTable('default');

        DB::connection('default')->exec("insert into users values (1, 'Jane', 'janedoe@example.com')");

        $data = function () {
            yield ['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com'];
            yield ['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@example.com'];
        };

        $loader = new InsertUpdate;

        $loader->load($data(), 'users');

        $expected = [
            ['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com'],
            ['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@example.com'],
        ];

        $query = DB::connection('default')->query('select * from users');

        $this->assertEquals($expected, $query->fetchAll());
    }

    /** @test */
    public function insert_and_update_specified_columns()
    {
        $this->createUsersTable('default');

        DB::connection('default')->exec("insert into users values (1, 'Jane', 'oldemail@example.com')");

        $data = function () {
            yield ['id' => '1', 'name' => 'Jane Doe', 'email' => 'newemail@example.com'];
            yield ['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@example.com'];
        };

        $loader = new InsertUpdate;

        $loader->columns = ['id', 'name'];

        $loader->load($data(), 'users');

        $expected = [
            ['id' => '1', 'name' => 'Jane Doe', 'email' => 'oldemail@example.com'],
            ['id' => '2', 'name' => 'John Doe', 'email' => ''],
        ];

        $query = DB::connection('default')->query('select * from users');

        $this->assertEquals($expected, $query->fetchAll());
    }

    /** @test */
    public function insert_and_update_data_with_timestamps()
    {
        $this->createUsersTable('default', true);

        $timestamp = '2005-03-24 15:00:00';

        DB::connection('default')->exec("insert into users values (1, 'Jane', 'jane@example.com', '$timestamp', '$timestamp', null)");

        $data = function () {
            yield ['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com'];
            yield ['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@example.com'];
        };

        $loader = new InsertUpdate;

        $loader->timestamps = true;

        $loader->load($data(), 'users');

        $expected = [
            ['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com', 'created_at' => '2005-03-24 15:00:00', 'updated_at' => date('Y-m-d G:i:s'), 'deleted_at' => null],
            ['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@example.com', 'created_at' => date('Y-m-d G:i:s'), 'updated_at' => date('Y-m-d G:i:s'), 'deleted_at' => null],
        ];

        $query = DB::connection('default')->query('select * from users');

        $this->assertEquals($expected, $query->fetchAll());
    }
}

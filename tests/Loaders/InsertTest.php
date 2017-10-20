<?php

namespace Tests\Loaders;

use Tests\TestCase;
use Marquine\Etl\Etl;
use Marquine\Etl\Loaders\Insert;

class InsertTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->createTables();
    }

    /** @test */
    function insert_data_into_the_database()
    {
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

        $query = Etl::database()->query()->select('users')->execute();

        $this->assertEquals($expected, $query->fetchAll());
    }

    /** @test */
    function insert_specified_into_the_database()
    {
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

        $query = Etl::database()->query()->select('users')->execute();

        $this->assertEquals($expected, $query->fetchAll());
    }

    /** @test */
    function insert_data_into_the_database_with_timestamps()
    {
        $data = function () {
            yield ['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com'];
            yield ['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@example.com'];
        };

        $loader = new Insert;

        $loader->timestamps = true;

        $loader->load($data(), 'users_ts');

        $expected = [
            ['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com', 'created_at' => date('Y-m-d G:i:s'), 'updated_at' => date('Y-m-d G:i:s'), 'deleted_at' => null],
            ['id' => '2', 'name' => 'John Doe', 'email' => 'johndoe@example.com', 'created_at' => date('Y-m-d G:i:s'), 'updated_at' => date('Y-m-d G:i:s'), 'deleted_at' => null],
        ];

        $query = Etl::database()->query()->select('users_ts')->execute();

        $this->assertEquals($expected, $query->fetchAll());
    }
}

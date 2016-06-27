<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Metis\Metis;
use Illuminate\Database\Capsule\Manager as DB;

class TableTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /** @test */
    function extract_data_from_a_database_table()
    {
        Metis::db()->insert('users', $this->users);

        $results = Metis::extract('table', 'users')->get();

        $this->assertEquals($this->users, $results);
    }

    /** @test */
    function extract_specific_columns_from_a_database_table()
    {
        Metis::db()->insert('users_ts', $this->users);

        $columns = ['id', 'name', 'email'];

        $results = Metis::extract('table', 'users_ts', $columns)->get();

        $this->assertEquals($this->users, $results);
    }

    /** @test */
    function extract_data_from_a_database_table_with_a_where_clause()
    {
        Metis::db()->insert('users', $this->users);

        $options = ['where' => ['id' => 1]];

        $results = Metis::extract('table', 'users', null, $options)->get();

        $expected = [['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function extract_data_from_a_database_table_using_a_custom_connection()
    {
        Metis::addConnection(['driver' => 'sqlite', 'database' => ':memory:'], 'connection_name');
        $this->migrateTables('connection_name');
        Metis::db('connection_name')->insert('users', $this->users);

        $options = ['connection' => 'connection_name'];

        $results = Metis::extract('table', 'users', null, $options)->get();

        $this->assertEquals($this->users, $results);
    }
}

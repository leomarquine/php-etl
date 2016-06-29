<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Metis\Metis;

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
        foreach ($this->users as $user) {
            Metis::connection()->insert('users', $user);
        }

        $results = Metis::extract('table', 'users')->get();

        $this->assertEquals($this->users, $results);
    }

    /** @test */
    function extract_specific_columns_from_a_database_table()
    {
        foreach ($this->users as $user) {
            Metis::connection()->insert('users_ts', $user);
        }

        $columns = ['id', 'name', 'email'];

        $results = Metis::extract('table', 'users_ts', $columns)->get();

        $this->assertEquals($this->users, $results);
    }

    /** @test */
    function extract_data_from_a_database_table_with_a_where_clause()
    {
        foreach ($this->users as $user) {
            Metis::connection()->insert('users', $user);
        }

        $options = ['where' => ['id' => 1]];

        $results = Metis::extract('table', 'users', null, $options)->get();

        $expected = [['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function extract_data_from_a_database_table_using_a_custom_connection()
    {
        Metis::addConnection(['driver' => 'pdo_sqlite', 'database' => ':memory:'], 'connection_name');

        $this->migrateTables('connection_name');

        foreach ($this->users as $user) {
            Metis::connection('connection_name')->insert('users', $user);
        }

        $options = ['connection' => 'connection_name'];

        $results = Metis::extract('table', 'users', null, $options)->get();

        $this->assertEquals($this->users, $results);
    }
}

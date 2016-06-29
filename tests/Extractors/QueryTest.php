<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Metis\Metis;

class QueryTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /** @test */
    function extract_data_from_a_database_using_a_custom_query()
    {
        foreach ($this->users as $user) {
            Metis::connection()->insert('users', $user);
        }

        $query = 'SELECT * FROM users';

        $results = Metis::extract('query', $query)->get();

        $this->assertEquals($this->users, $results);
    }

    /** @test */
    function extract_data_from_a_database_using_a_custom_query_and_bindings()
    {
        foreach ($this->users as $user) {
            Metis::connection()->insert('users', $user);
        }

        $query = 'SELECT * FROM users WHERE id = ?';

        $bindings = [1];

        $results = Metis::extract('query', $query, $bindings)->get();

        $expected = [['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function extract_data_from_a_database_using_a_custom_query_and_named_bindings()
    {
        foreach ($this->users as $user) {
            Metis::connection()->insert('users', $user);
        }

        $query = 'SELECT * FROM users WHERE id = :id AND name = :name';

        $bindings = ['name' => 'John Doe', 'id' => 1];

        $results = Metis::extract('query', $query, $bindings)->get();

        $expected = [['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function extract_data_from_a_database_using_a_custom_query_and_connection()
    {
        Metis::addConnection(['driver' => 'pdo_sqlite', 'database' => ':memory:'], 'test');

        $this->migrateTables('test');

        foreach ($this->users as $user) {
            Metis::connection('test')->insert('users', $user);
        }

        $query = 'SELECT * FROM users';

        $options = ['connection' => 'test'];

        $results = Metis::extract('query', $query, null, $options)->get();

        $this->assertEquals($this->users, $results);
    }
}

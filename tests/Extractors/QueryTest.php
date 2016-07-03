<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Metis\Metis;
use Marquine\Metis\Extractors\Query;

class QueryTest extends TestCase
{
    protected $items = [
        ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /** @test */
    function extract_data_from_a_database_using_a_custom_query()
    {
        foreach ($this->items as $user) {
            Metis::connection()->insert('users', $user);
        }

        $query = 'SELECT * FROM users';

        $extractor = new Query;

        $results = $extractor->extract($query);

        $this->assertEquals($this->items, $results);
    }

    /** @test */
    function extract_data_from_a_database_using_a_custom_query_and_bindings()
    {
        foreach ($this->items as $user) {
            Metis::connection()->insert('users', $user);
        }

        $query = 'SELECT * FROM users WHERE id = ?';

        $bindings = [1];

        $extractor = new Query;

        $results = $extractor->extract($query, $bindings);

        $expected = [['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function extract_data_from_a_database_using_a_custom_query_and_named_bindings()
    {
        foreach ($this->items as $user) {
            Metis::connection()->insert('users', $user);
        }

        $query = 'SELECT * FROM users WHERE id = :id AND name = :name';

        $bindings = ['name' => 'John Doe', 'id' => 1];

        $extractor = new Query;

        $results = $extractor->extract($query, $bindings);

        $expected = [['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function extract_data_from_a_database_using_a_custom_query_and_connection()
    {
        Metis::addConnection(['driver' => 'pdo_sqlite', 'database' => ':memory:'], 'test');

        $this->migrateTables('test');

        foreach ($this->items as $user) {
            Metis::connection('test')->insert('users', $user);
        }

        $query = 'SELECT * FROM users';

        $options = ['connection' => 'test'];

        $extractor = new Query($options);

        $results = $extractor->extract($query, null, $options);

        $this->assertEquals($this->items, $results);
    }
}

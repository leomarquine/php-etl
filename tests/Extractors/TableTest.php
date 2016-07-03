<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Metis\Metis;
use Marquine\Metis\Extractors\Table;

class TableTest extends TestCase
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
    function extract_data_from_a_database_table()
    {
        foreach ($this->items as $user) {
            Metis::connection()->insert('users', $user);
        }

        $extractor = new Table;

        $results = $extractor->extract('users');

        $this->assertEquals($this->items, $results);
    }

    /** @test */
    function extract_specific_columns_from_a_database_table()
    {
        foreach ($this->items as $user) {
            Metis::connection()->insert('users_ts', $user);
        }

        $columns = ['id', 'name', 'email'];

        $extractor = new Table;

        $results = $extractor->extract('users_ts', $columns);

        $this->assertEquals($this->items, $results);
    }

    /** @test */
    function extract_data_from_a_database_table_with_a_where_clause()
    {
        foreach ($this->items as $user) {
            Metis::connection()->insert('users', $user);
        }

        $options = ['where' => ['id' => 1]];

        $extractor = new Table($options);

        $results = $extractor->extract('users', null, $options);

        $expected = [['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function extract_data_from_a_database_table_using_a_custom_connection()
    {
        Metis::addConnection(['driver' => 'pdo_sqlite', 'database' => ':memory:'], 'connection_name');

        $this->migrateTables('connection_name');

        foreach ($this->items as $user) {
            Metis::connection('connection_name')->insert('users', $user);
        }

        $options = ['connection' => 'connection_name'];

        $extractor = new Table($options);

        $results = $extractor->extract('users', null, $options);

        $this->assertEquals($this->items, $results);
    }
}

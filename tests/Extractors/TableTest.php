<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Etl;
use Marquine\Etl\Extractors\Table;

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
        Etl::connection()->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        Etl::connection()->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $extractor = new Table;

        $results = $extractor->extract('users');

        $this->assertEquals($this->items, $results);
    }

    /** @test */
    function extract_specific_columns_from_a_database_table()
    {
        Etl::connection()->exec("insert into users_ts (id, name, email) values (1, 'John Doe', 'johndoe@email.com')");
        Etl::connection()->exec("insert into users_ts (id, name, email) values (2, 'Jane Doe', 'janedoe@email.com')");

        $extractor = new Table;

        $extractor->columns = ['id', 'name', 'email'];

        $results = $extractor->extract('users_ts');

        $this->assertEquals($this->items, $results);
    }

    /** @test */
    function extract_data_from_a_database_table_with_a_where_clause()
    {
        Etl::connection()->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        Etl::connection()->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $extractor = new Table;

        $extractor->where = ['id' => 1];

        $results = $extractor->extract('users');

        $expected = [['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function extract_data_from_a_database_table_using_a_custom_connection()
    {
        Etl::addConnection(['driver' => 'sqlite', 'database' => ':memory:'], 'test');

        $this->migrateTables('test');

        Etl::connection('test')->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        Etl::connection('test')->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $extractor = new Table;

        $extractor->connection = 'test';

        $results = $extractor->extract('users');

        $this->assertEquals($this->items, $results);
    }
}

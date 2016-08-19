<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Etl;
use Marquine\Etl\Extractors\Table;

class TableTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->createTables();
    }

    protected $items = [
        ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    function extract_data_from_a_database_table()
    {
        $extractor = new Table;

        Etl::database()->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        Etl::database()->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $results = $extractor->extract('users');

        $this->assertEquals($this->items, $results);
    }

    /** @test */
    function extract_specific_columns_from_a_database_table()
    {
        Etl::database()->exec("insert into users_ts (id, name, email) values (1, 'John Doe', 'johndoe@email.com')");
        Etl::database()->exec("insert into users_ts (id, name, email) values (2, 'Jane Doe', 'janedoe@email.com')");

        $extractor = new Table;

        $extractor->columns = ['id', 'name', 'email'];

        $results = $extractor->extract('users_ts');

        $this->assertEquals($this->items, $results);
    }

    /** @test */
    function extract_data_from_a_database_table_with_a_where_clause()
    {
        Etl::database()->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        Etl::database()->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $extractor = new Table;

        $extractor->where = ['id' => 1];

        $results = $extractor->extract('users');

        $expected = [['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function extract_data_from_a_database_table_using_a_custom_connection()
    {
        $this->createTables('secondary');
        Etl::database('secondary')->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        Etl::database('secondary')->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $extractor = new Table;

        $extractor->connection = 'secondary';

        $results = $extractor->extract('users');

        $this->assertEquals($this->items, $results);
    }
}

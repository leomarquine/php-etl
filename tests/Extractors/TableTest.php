<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\Table;
use Marquine\Etl\Database\Manager as DB;

class TableTest extends TestCase
{
    protected $items = [
        ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    public function extract_data_from_a_database_table()
    {
        $this->createUsersTable('default');

        $extractor = new Table;

        DB::connection('default')->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        DB::connection('default')->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $results = $extractor->extract('users');

        $this->assertEquals($this->items, iterator_to_array($results));
    }

    /** @test */
    public function extract_specific_columns_from_a_database_table()
    {
        $this->createUsersTable('default', true);

        DB::connection('default')->exec("insert into users (id, name, email) values (1, 'John Doe', 'johndoe@email.com')");
        DB::connection('default')->exec("insert into users (id, name, email) values (2, 'Jane Doe', 'janedoe@email.com')");

        $extractor = new Table;

        $extractor->columns = ['id', 'name', 'email'];

        $results = $extractor->extract('users');

        $this->assertEquals($this->items, iterator_to_array($results));
    }

    /** @test */
    public function extract_data_from_a_database_table_with_a_where_clause()
    {
        $this->createUsersTable('default');

        DB::connection('default')->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        DB::connection('default')->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $extractor = new Table;

        $extractor->where = ['id' => 1];

        $results = $extractor->extract('users');

        $expected = [['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']];

        $this->assertEquals($expected, iterator_to_array($results));
    }

    /** @test */
    public function extract_data_from_a_database_table_using_a_custom_connection()
    {
        $this->createUsersTable('secondary');

        DB::connection('secondary')->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        DB::connection('secondary')->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $extractor = new Table;

        $extractor->connection = 'secondary';

        $results = $extractor->extract('users');

        $this->assertEquals($this->items, iterator_to_array($results));
    }
}

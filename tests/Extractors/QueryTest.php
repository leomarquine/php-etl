<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\Query;
use Marquine\Etl\Database\Manager as DB;

class QueryTest extends TestCase
{
    protected $items = [
        ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    /** @test */
    public function extract_data_from_a_database_using_a_custom_query()
    {
        $this->createUsersTable('default');

        DB::connection('default')->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        DB::connection('default')->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $query = 'SELECT * FROM users';

        $extractor = new Query;

        $results = $extractor->extract($query);

        $this->assertEquals($this->items, iterator_to_array($results));
    }

    /** @test */
    public function extract_data_from_a_database_using_a_custom_query_and_bindings()
    {
        $this->createUsersTable('default');

        DB::connection('default')->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        DB::connection('default')->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $query = 'SELECT * FROM users WHERE id = ?';

        $extractor = new Query;

        $extractor->bindings = [1];

        $results = $extractor->extract($query);

        $expected = [['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']];

        $this->assertEquals($expected, iterator_to_array($results));
    }

    /** @test */
    public function extract_data_from_a_database_using_a_custom_query_and_named_bindings()
    {
        $this->createUsersTable('default');

        DB::connection('default')->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        DB::connection('default')->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $query = 'SELECT * FROM users WHERE id = :id AND name = :name';

        $extractor = new Query;

        $extractor->bindings = ['name' => 'John Doe', 'id' => 1];

        $results = $extractor->extract($query);

        $expected = [['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']];

        $this->assertEquals($expected, iterator_to_array($results));
    }

    /** @test */
    public function extract_data_from_a_database_using_a_custom_query_and_connection()
    {
        $this->createUsersTable('secondary');

        DB::connection('secondary')->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        DB::connection('secondary')->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $query = 'SELECT * FROM users';

        $extractor = new Query;

        $extractor->connection = 'secondary';

        $results = $extractor->extract($query);

        $this->assertEquals($this->items, iterator_to_array($results));
    }
}

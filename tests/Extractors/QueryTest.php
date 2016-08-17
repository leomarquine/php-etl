<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Etl;
use Marquine\Etl\Traits\Database;
use Marquine\Etl\Extractors\Query;

class QueryTest extends TestCase
{
    use Database;

    protected $items = [
        ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->connect('primary');
        $this->db->exec('delete from users; delete from users_ts');
    }

    /** @test */
    function extract_data_from_a_database_using_a_custom_query()
    {
        $this->db->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        $this->db->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $query = 'SELECT * FROM users';

        $extractor = new Query;

        $results = $extractor->extract($query);

        $this->assertEquals($this->items, $results);
    }

    /** @test */
    function extract_data_from_a_database_using_a_custom_query_and_bindings()
    {
        $this->db->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        $this->db->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $query = 'SELECT * FROM users WHERE id = ?';

        $extractor = new Query;

        $extractor->bindings = [1];

        $results = $extractor->extract($query);

        $expected = [['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function extract_data_from_a_database_using_a_custom_query_and_named_bindings()
    {
        $this->db->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        $this->db->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $query = 'SELECT * FROM users WHERE id = :id AND name = :name';

        $extractor = new Query;

        $extractor->bindings = ['name' => 'John Doe', 'id' => 1];

        $results = $extractor->extract($query);

        $expected = [['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']];

        $this->assertEquals($expected, $results);
    }

    /** @test */
    function extract_data_from_a_database_using_a_custom_query_and_connection()
    {
        $this->connect('secondary');
        $this->db->exec('delete from users; delete from users_ts');
        $this->db->exec("insert into users values (1, 'John Doe', 'johndoe@email.com')");
        $this->db->exec("insert into users values (2, 'Jane Doe', 'janedoe@email.com')");

        $query = 'SELECT * FROM users';

        $extractor = new Query;

        $extractor->connection = 'secondary';

        $results = $extractor->extract($query);

        $this->assertEquals($this->items, $results);
    }
}

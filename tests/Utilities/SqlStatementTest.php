<?php

namespace Tests\Utilities;

use Tests\TestCase;
use Marquine\Etl\Database\Manager as DB;
use Marquine\Etl\Utilities\SqlStatement;

class SqlStatementTest extends TestCase
{
    /** @test */
    public function execute_a_sql_statement()
    {
        $this->createUsersTable('default');

        DB::connection('default')->exec("insert into users values (1, 'Jane Doe', 'janedoe@example.com')");

        $this->assertCount(1, DB::connection('default')->query('select * from users')->fetchAll());

        $utility = new SqlStatement;

        $utility->statement = 'delete from users';

        $utility->run();

        $this->assertCount(0, DB::connection('default')->query('select * from users')->fetchAll());
    }
}

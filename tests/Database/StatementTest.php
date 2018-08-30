<?php

namespace Tests\Database;

use Tests\TestCase;
use Marquine\Etl\Database\Statement;

class StatementTest extends TestCase
{
    /** @test */
    public function select()
    {
        $statement = new Statement($this->createMock('PDO'));
        $statement->select('users');

        $this->assertEquals('select * from users', $statement->toSql());

        $statement = new Statement($this->createMock('PDO'));
        $statement->select('users', ['name', 'email']);

        $this->assertEquals('select name, email from users', $statement->toSql());
    }

    /** @test */
    public function insert()
    {
        $statement = new Statement($this->createMock('PDO'));
        $statement->insert('users', ['name', 'email']);

        $this->assertEquals('insert into users (name, email) values (:name, :email)', $statement->toSql());
    }

    /** @test */
    public function update()
    {
        $statement = new Statement($this->createMock('PDO'));
        $statement->update('users', ['name', 'email']);

        $this->assertEquals('update users set name = :name, email = :email', $statement->toSql());
    }

    /** @test */
    public function delete()
    {
        $statement = new Statement($this->createMock('PDO'));
        $statement->delete('users');

        $this->assertEquals('delete from users', $statement->toSql());
    }

    /** @test */
    public function where()
    {
        $statement = new Statement($this->createMock('PDO'));
        $statement->where(['name', 'email']);

        $this->assertEquals('where name = :name and email = :email', $statement->toSql());
    }

    /** @test */
    public function prepare()
    {
        $pdoStatement = $this->createMock('PDOStatement');

        $pdo = $this->createMock('PDO');
        $pdo->expects($this->once())->method('prepare')->with('')->willReturn($pdoStatement);

        $statement = new Statement($pdo);

        $this->assertInstanceOf('PDOStatement', $statement->prepare());
    }
}

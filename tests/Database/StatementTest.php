<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Database;

use Tests\TestCase;
use Wizaplace\Etl\Database\Statement;

class StatementTest extends TestCase
{
    /** @test */
    public function select()
    {
        $statement = new Statement($this->createMock('PDO'));
        $statement->select('users');

        static::assertEquals('select * from users', $statement->toSql());

        $statement = new Statement($this->createMock('PDO'));
        $statement->select('users', ['name', 'email']);

        static::assertEquals('select name, email from users', $statement->toSql());
    }

    /** @test */
    public function insert()
    {
        $statement = new Statement($this->createMock('PDO'));
        $statement->insert('users', ['name', 'email']);

        static::assertEquals('insert into users (name, email) values (:name, :email)', $statement->toSql());
    }

    /** @test */
    public function update()
    {
        $statement = new Statement($this->createMock('PDO'));
        $statement->update('users', ['name', 'email']);

        static::assertEquals('update users set name = :name, email = :email', $statement->toSql());
    }

    /** @test */
    public function delete()
    {
        $statement = new Statement($this->createMock('PDO'));
        $statement->delete('users');

        static::assertEquals('delete from users', $statement->toSql());
    }

    /** @test */
    public function where()
    {
        $statement = new Statement($this->createMock('PDO'));
        $statement->where(['name', 'email']);

        static::assertEquals('where name = :name and email = :email', $statement->toSql());
    }

    /** @test */
    public function prepare()
    {
        $pdoStatement = $this->createMock('PDOStatement');

        $pdo = $this->createMock('PDO');
        $pdo->expects($this->once())->method('prepare')->with('')->willReturn($pdoStatement);

        $statement = new Statement($pdo);

        static::assertInstanceOf('PDOStatement', $statement->prepare());
    }
}

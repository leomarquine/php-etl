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
use Wizaplace\Etl\Database\Query;

class QueryTest extends TestCase
{
    /** @test */
    public function select()
    {
        $query = new Query($this->createMock('PDO'));
        $query->select('users');

        static::assertEquals('select * from users', $query->toSql());

        $query = new Query($this->createMock('PDO'));
        $query->select('users', ['name', 'email']);

        static::assertEquals('select name, email from users', $query->toSql());
    }

    /** @test */
    public function insert()
    {
        $query = new Query($this->createMock('PDO'));
        $query->insert('users', ['name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        static::assertEquals('insert into users (name, email) values (?, ?)', $query->toSql());
        static::assertEquals(['Jane Doe', 'janedoe@example.com'], $query->getBindings());
    }

    /** @test */
    public function update()
    {
        $query = new Query($this->createMock('PDO'));
        $query->update('users', ['name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        static::assertEquals('update users set name = ?, email = ?', $query->toSql());
        static::assertEquals(['Jane Doe', 'janedoe@example.com'], $query->getBindings());
    }

    /** @test */
    public function delete()
    {
        $query = new Query($this->createMock('PDO'));
        $query->delete('users');

        static::assertEquals('delete from users', $query->toSql());
        static::assertEquals([], $query->getBindings());
    }

    /** @test */
    public function where()
    {
        $query = new Query($this->createMock('PDO'));
        $query->where(['name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        static::assertEquals('where name = ? and email = ?', $query->toSql());
        static::assertEquals(['Jane Doe', 'janedoe@example.com'], $query->getBindings());
    }

    /** @test */
    public function where_in()
    {
        $query = new Query($this->createMock('PDO'));
        $query->whereIn('id', ['1', '2']);

        static::assertEquals('where id in (?, ?)', $query->toSql());
        static::assertEquals(['1', '2'], $query->getBindings());
    }

    /** @test */
    public function where_not_in()
    {
        $query = new Query($this->createMock('PDO'));
        $query->whereNotIn('id', ['1', '2']);

        static::assertEquals('where id not in (?, ?)', $query->toSql());
        static::assertEquals(['1', '2'], $query->getBindings());
    }

    /** @test */
    public function composite_where_in()
    {
        $query = new Query($this->createMock('PDO'));
        $query->whereIn(['id', 'company'], [['id' => '1', 'company' => '1'], ['id' => '2', 'company' => '1']]);

        static::assertEquals('where (company, id) in ((?, ?), (?, ?))', $query->toSql());
        static::assertEquals(['1', '1', '1', '2'], $query->getBindings());
    }

    /** @test */
    public function composite_where_not_in()
    {
        $query = new Query($this->createMock('PDO'));
        $query->whereNotIn(['id', 'company'], [['id' => '1', 'company' => '1'], ['id' => '2', 'company' => '1']]);

        static::assertEquals('where (company, id) not in ((?, ?), (?, ?))', $query->toSql());
        static::assertEquals(['1', '1', '1', '2'], $query->getBindings());
    }

    /** @test */
    public function execute_query()
    {
        $statement = $this->createMock('PDOStatement');
        $statement->expects($this->once())->method('execute')->with([]);

        $pdo = $this->createMock('PDO');
        $pdo->expects($this->once())->method('prepare')->with('')->willReturn($statement);

        $query = new Query($pdo);

        static::assertInstanceOf('PDOStatement', $query->execute());
    }
}

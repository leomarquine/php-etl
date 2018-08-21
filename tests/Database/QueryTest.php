<?php

namespace Tests\Database;

use PDOStatement;
use Tests\TestCase;
use Marquine\Etl\Database\Query;
use Marquine\Etl\Database\Connection;

class QueryTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->connection = $this->getMockBuilder(Connection::class)->setMethods(['prepare'])->disableOriginalConstructor()->getMock();
        $this->statement = $this->createMock(PDOStatement::class);
    }

    /** @test */
    public function select()
    {
        $query = new Query($this->connection);
        $query->select('users');

        $this->assertEquals('select * from users', $query->toSql());

        $query = new Query($this->connection);
        $query->select('users', ['name', 'email']);

        $this->assertEquals('select name, email from users', $query->toSql());
    }

    /** @test */
    public function insert()
    {
        $query = new Query($this->connection);
        $query->insert('users', ['name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $this->assertEquals('insert into users (name, email) values (?, ?)', $query->toSql());
        $this->assertEquals(['Jane Doe', 'janedoe@example.com'], $query->getBindings());
    }

    /** @test */
    public function update()
    {
        $query = new Query($this->connection);
        $query->update('users', ['name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $this->assertEquals('update users set name = ?, email = ?', $query->toSql());
        $this->assertEquals(['Jane Doe', 'janedoe@example.com'], $query->getBindings());
    }

    /** @test */
    public function delete()
    {
        $query = new Query($this->connection);
        $query->delete('users');

        $this->assertEquals('delete from users', $query->toSql());
        $this->assertEquals([], $query->getBindings());
    }

    /** @test */
    public function where()
    {
        $query = new Query($this->connection);
        $query->where(['name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $this->assertEquals('where name = ? and email = ?', $query->toSql());
        $this->assertEquals(['Jane Doe', 'janedoe@example.com'], $query->getBindings());
    }

    /** @test */
    public function where_in()
    {
        $query = new Query($this->connection);
        $query->whereIn('id', ['1', '2']);

        $this->assertEquals('where id in (?, ?)', $query->toSql());
        $this->assertEquals(['1', '2'], $query->getBindings());
    }

    /** @test */
    public function where_not_in()
    {
        $query = new Query($this->connection);
        $query->whereNotIn('id', ['1', '2']);

        $this->assertEquals('where id not in (?, ?)', $query->toSql());
        $this->assertEquals(['1', '2'], $query->getBindings());
    }

    /** @test */
    public function composite_where_in()
    {
        $query = new Query($this->connection);
        $query->whereIn(['id', 'company'], [['id' => '1', 'company' => '1'], ['id' => '2', 'company' => '1']]);

        $this->assertEquals('where (company, id) in ((?, ?), (?, ?))', $query->toSql());
        $this->assertEquals(['1', '1', '1', '2'], $query->getBindings());
    }

    /** @test */
    public function composite_where_not_in()
    {
        $query = new Query($this->connection);
        $query->whereNotIn(['id', 'company'], [['id' => '1', 'company' => '1'], ['id' => '2', 'company' => '1']]);

        $this->assertEquals('where (company, id) not in ((?, ?), (?, ?))', $query->toSql());
        $this->assertEquals(['1', '1', '1', '2'], $query->getBindings());
    }

    /** @test */
    public function execute()
    {
        $this->connection->expects($this->once())->method('prepare')->with('')->willReturn($this->statement);
        $this->statement->expects($this->once())->method('execute')->with([]);

        $query = new Query($this->connection);

        $this->assertInstanceOf(PDOStatement::class, $query->execute());
    }
}

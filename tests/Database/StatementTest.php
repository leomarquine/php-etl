<?php

namespace Tests\Database;

use Mockery;
use PDOStatement;
use Tests\TestCase;
use Marquine\Etl\Database\Statement;
use Marquine\Etl\Database\Connection;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class StatementTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected $connection;

    protected $statement;

    protected function setUp()
    {
        parent::setUp();

        $this->connection = Mockery::mock(Connection::class);
        $this->statement = Mockery::mock(PDOStatement::class);
    }

    /** @test */
    function select()
    {
        $statement = new Statement($this->connection);

        $statement->select('users');

        $this->assertEquals('select * from users', $statement->toSql());

        $statement = new Statement($this->connection);

        $statement->select('users', ['name', 'email']);

        $this->assertEquals('select name, email from users', $statement->toSql());
    }

    /** @test */
    function insert()
    {
        $statement = new Statement($this->connection);

        $statement->insert('users', ['name', 'email']);

        $this->assertEquals('insert into users (name, email) values (:name, :email)', $statement->toSql());
    }

    /** @test */
    function update()
    {
        $statement = new Statement($this->connection);

        $statement->update('users', ['name', 'email']);

        $this->assertEquals('update users set name = :name, email = :email', $statement->toSql());
    }

    /** @test */
    function delete()
    {
        $statement = new Statement($this->connection);

        $statement->delete('users');

        $this->assertEquals('delete from users', $statement->toSql());
    }

    /** @test */
    function where()
    {
        $statement = new Statement($this->connection);

        $statement->where(['name', 'email']);

        $this->assertEquals('where name = :name and email = :email', $statement->toSql());
    }

    /** @test */
    function prepare()
    {
        $this->connection->shouldReceive('prepare')->once()->with('')->andReturn($this->statement);

        $statement = new Statement($this->connection);

        $this->assertInstanceOf(PDOStatement::class, $statement->prepare());
    }
}

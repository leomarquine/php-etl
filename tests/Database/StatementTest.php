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
use Wizaplace\Etl\Database\ConnectionFactory;
use Wizaplace\Etl\Database\Manager;
use Wizaplace\Etl\Database\Statement;

class StatementTest extends TestCase
{
    /** @test */
    public function select(): void
    {
        $statement = new Statement($this->createMock('PDO'));
        $statement->select('users');

        static::assertEquals('select * from users', $statement->toSql());

        $statement = new Statement($this->createMock('PDO'));
        $statement->select('users', ['name', 'email']);

        static::assertEquals('select name, email from users', $statement->toSql());
    }

    /** @test */
    public function insert(): void
    {
        $statement = new Statement($this->createMock('PDO'));
        $statement->insert('users', ['name', 'email']);

        static::assertEquals('insert into users (name, email) values (:name, :email)', $statement->toSql());
    }

    /** @test */
    public function update(): void
    {
        $statement = new Statement($this->createMock('PDO'));
        $statement->update('users', ['name', 'email']);

        static::assertEquals('update users set name = :name, email = :email', $statement->toSql());
    }

    /** @test */
    public function delete(): void
    {
        $statement = new Statement($this->createMock('PDO'));
        $statement->delete('users');

        static::assertEquals('delete from users', $statement->toSql());
    }

    /** @test */
    public function where(): void
    {
        $statement = new Statement($this->createMock('PDO'));
        $statement->where(['name', 'email']);

        static::assertEquals('where name = :name and email = :email', $statement->toSql());
    }

    /** @test */
    public function prepare(): void
    {
        $pdoStatement = $this->createMock('PDOStatement');

        $pdo = $this->createMock('PDO');
        $pdo->expects(static::once())->method('prepare')->with('')->willReturn($pdoStatement);

        $statement = new Statement($pdo);

        static::assertInstanceOf('PDOStatement', $statement->prepare());
    }

    /** @test */
    public function prepareInvalid(): void
    {
        // Set up connection to SQLite test database.
        $connection = 'default';
        $name = tempnam(sys_get_temp_dir(), 'etl');
        $config = ['driver' => 'sqlite', 'database' => $name];
        $manager = new Manager(new ConnectionFactory());
        $manager->addConnection($config, $connection);

        // Instantiate a table for testing.
        $database = $manager->pdo($connection);
        $database->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);

        $statement = new Statement($database);
        $statement->select('foo', ['>']);

        try {
            $statement->prepare();
            static::fail('An exception should have been thrown');
        } catch (\PDOException $exception) {
            static::assertEquals('SQLSTATE[HY000]: General error: 1 near ">": syntax error', $exception->getMessage());
        } catch (\Exception $exception) {
            static::fail('An instance of ' . \PDOException::class . ' should have been thrown');
        }
    }
}

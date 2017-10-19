<?php

namespace Tests\Database;

use PDO;
use Mockery;
use Tests\TestCase;
use Marquine\Etl\Database\Query;
use Marquine\Etl\Database\Statement;
use Marquine\Etl\Database\Connection;
use Marquine\Etl\Database\Transaction;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class ConnectionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected $pdo;

    protected $connection;

    protected function setUp()
    {
        parent::setUp();

        $this->pdo = Mockery::mock(PDO::class);
        $this->connection = new Connection($this->pdo);
    }

    /** @test */
    function get_a_new_query_instance()
    {
        $this->assertInstanceOf(Query::class, $this->connection->query());
    }

    /** @test */
    function get_a_new_statement_instance()
    {
        $this->assertInstanceOf(Statement::class, $this->connection->statement());
    }

    /** @test */
    function get_a_new_transaction_instance()
    {
        $this->assertInstanceOf(Transaction::class, $this->connection->transaction(true));
    }

    /** @test */
    function dynamically_pass_method_calls_to_the_pdo_instance()
    {
        $this->pdo->shouldReceive('method')->once()->with('param');

        $this->connection->method('param');
    }
}

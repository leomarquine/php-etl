<?php

namespace Tests\Database;

use Mockery;
use Exception;
use Tests\TestCase;
use InvalidArgumentException;
use Marquine\Etl\Database\Connection;
use Marquine\Etl\Database\Transaction;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class TransactionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected $pdo;

    protected $transaction;

    protected function setUp()
    {
        parent::setUp();

        $this->connection = Mockery::mock(Connection::class);
        $this->transaction = new Transaction($this->connection);
    }

    /** @test */
    public function no_transaction()
    {
        $data = range(1, 20);
        $mode = 'none';
        $callback = function ($row) {
            return $row;
        };

        $this->connection->shouldNotReceive('beginTransaction');
        $this->connection->shouldNotReceive('commit');
        $this->connection->shouldNotReceive('rollBack');

        $this->assertEquals($data, $this->transaction->mode($mode)->data($data)->run($callback));
    }

    /** @test */
    public function single_transaction()
    {
        $data = range(1, 20);
        $mode = 'single';
        $callback = function ($row) {
            return $row;
        };

        $this->connection->shouldReceive('beginTransaction')->once();
        $this->connection->shouldReceive('commit')->once();

        $this->assertEquals($data, $this->transaction->mode($mode)->data($data)->run($callback));
    }

    /** @test */
    public function transaction_size_is_multiple_of_data_size()
    {
        $data = range(1, 20);
        $mode = 10;
        $callback = function ($row) {
            return $row;
        };

        $this->connection->shouldReceive('beginTransaction')->times(2);
        $this->connection->shouldReceive('commit')->times(2);

        $this->assertEquals($data, $this->transaction->mode($mode)->data($data)->run($callback));
    }

    /** @test */
    public function transaction_size_is_not_multiple_of_data_size()
    {
        $data = range(1, 17);
        $mode = 10;
        $callback = function ($row) {
            return $row;
        };

        $this->connection->shouldReceive('beginTransaction')->times(2);
        $this->connection->shouldReceive('commit')->times(2);

        $this->assertEquals($data, $this->transaction->mode($mode)->data($data)->run($callback));
    }

    /** @test */
    public function transaction_rollback_on_error()
    {
        $data = range(1, 20);
        $mode = 10;
        $callback = function () {
            throw new Exception;
        };

        $this->connection->shouldReceive('beginTransaction')->times(1);
        $this->connection->shouldReceive('rollBack')->times(1);

        $this->expectException(Exception::class);

        $this->transaction->mode($mode)->data($data)->run($callback);
    }

    /** @test */
    public function invalid_transaction_mode_throws_an_exception()
    {
        $data = range(1, 20);
        $mode = 'invalid';
        $callback = function () {
        };

        $this->expectException(InvalidArgumentException::class);

        $this->transaction->mode($mode)->data($data)->run($callback);
    }
}

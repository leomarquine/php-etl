<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Tests\Database;

use Exception;
use Tests\TestCase;
use Wizaplace\Etl\Database\Transaction;

class TransactionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = $this->createMock('PDO');
        $this->callback = $this->getMockBuilder('stdClass')->setMethods(['callback'])->getMock();

        $this->transaction = new Transaction($this->connection);
    }

    protected function transaction($range)
    {
        foreach ($range as $current) {
            $this->transaction->run([$this->callback, 'callback']);
        }

        $this->transaction->close();
    }

    /** @test */
    public function runs_a_single_transaction_if_size_is_empty()
    {
        $this->callback->expects($this->exactly(4))->method('callback');

        $this->connection->expects($this->exactly(1))->method('beginTransaction');
        $this->connection->expects($this->exactly(0))->method('rollBack');
        $this->connection->expects($this->exactly(1))->method('commit');

        $this->transaction(range(1, 4));
    }

    /** @test */
    public function runs_transactions_when_commit_size_is_multiple_of_total_lines()
    {
        $this->callback->expects($this->exactly(4))->method('callback');

        $this->connection->expects($this->exactly(2))->method('beginTransaction');
        $this->connection->expects($this->exactly(0))->method('rollBack');
        $this->connection->expects($this->exactly(2))->method('commit');

        $this->transaction->size(2);

        $this->transaction(range(1, 4));
    }

    /** @test */
    public function runs_transactions_when_commit_size_is_not_multiple_of_total_lines()
    {
        $this->callback->expects($this->exactly(3))->method('callback');

        $this->connection->expects($this->exactly(2))->method('beginTransaction');
        $this->connection->expects($this->exactly(0))->method('rollBack');
        $this->connection->expects($this->exactly(2))->method('commit');

        $this->transaction->size(2);

        $this->transaction(range(1, 3));
    }

    /** @test */
    public function rolls_back_the_last_transaction_and_stops_execution_on_error()
    {
        $this->callback->expects($this->exactly(3))->method('callback')->willReturnOnConsecutiveCalls(
            null,
            null,
            $this->throwException(new Exception())
        );

        $this->connection->expects($this->exactly(2))->method('beginTransaction');
        $this->connection->expects($this->exactly(1))->method('rollBack');
        $this->connection->expects($this->exactly(1))->method('commit');

        $this->transaction->size(2);

        $this->expectException('Exception');

        $this->transaction(range(1, 4));
    }
}

<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Database;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;
use Wizaplace\Etl\Database\Transaction;

class TransactionTest extends TestCase
{
    private Transaction $transaction;

    /** @var MockObject|\stdClass */
    private $callback;

    /** @var \PDO|MockObject */
    private $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = $this->createMock('PDO');
        $this->callback = $this->getMockBuilder('stdClass')->addMethods(['callback'])->getMock();

        $this->transaction = new Transaction($this->connection);
    }

    protected function transaction(array $range): void
    {
        foreach ($range as $current) {
            $this->transaction->run([$this->callback, 'callback']);
        }

        $this->transaction->close();
    }

    /** @test */
    public function runsSingleTransactionIfSizeIsEmpty(): void
    {
        $this->callback->expects(static::exactly(4))->method('callback');

        $this->connection->expects(static::exactly(1))->method('beginTransaction');
        $this->connection->expects(static::exactly(0))->method('rollBack');
        $this->connection->expects(static::exactly(1))->method('commit');

        $this->transaction(range(1, 4));
    }

    /** @test */
    public function runsTransactionsWhenCommitSizeIsMultipleOfTotalLines(): void
    {
        $this->callback->expects(static::exactly(4))->method('callback');

        $this->connection->expects(static::exactly(2))->method('beginTransaction');
        $this->connection->expects(static::exactly(0))->method('rollBack');
        $this->connection->expects(static::exactly(2))->method('commit');

        $this->transaction->size(2);

        $this->transaction(range(1, 4));
    }

    /** @test */
    public function runsTransactionsWhenCommitSizeIsNotMultipleOfTotalLines(): void
    {
        $this->callback->expects(static::exactly(3))->method('callback');

        $this->connection->expects(static::exactly(2))->method('beginTransaction');
        $this->connection->expects(static::exactly(0))->method('rollBack');
        $this->connection->expects(static::exactly(2))->method('commit');

        $this->transaction->size(2);

        $this->transaction(range(1, 3));
    }

    /** @test */
    public function rollsBackTheLastTransactionAndStopsExecutionOnError(): void
    {
        $this->callback->expects(static::exactly(3))->method('callback')->willReturnOnConsecutiveCalls(
            null,
            null,
            static::throwException(new Exception())
        );

        $this->connection->expects(static::exactly(2))->method('beginTransaction');
        $this->connection->expects(static::exactly(1))->method('rollBack');
        $this->connection->expects(static::exactly(1))->method('commit');

        $this->transaction->size(2);

        $this->expectException('Exception');

        $this->transaction(range(1, 4));
    }
}

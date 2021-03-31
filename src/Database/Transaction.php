<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Database;

class Transaction
{
    /**
     * The database connection.
     */
    protected \PDO $pdo;

    /**
     * Current transaction count.
     */
    protected int $count = 0;

    /**
     * Indicates if a transaction is open.
     */
    protected bool $open = false;

    /**
     * Commit size.
     */
    protected int $size = 0;

    /**
     * Create a new Transaction instance.
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Code defensively by closing any open transactions when this object is destroyed.
     *
     * If this work in done in a single transaction, we want to roll back that transaction if in insert fails. In
     * that manner, the ETL process becomes ACID in that either all of the inserts are committed or none are. If
     * the ETL process fails, we can replay the entire source after fixing the error.
     *
     * If the work is done in multiple transactions, however, some transactions may have already been committed. The
     * inserts from later pending transactions therefore are not atomic or durable in the sense that the pipeline
     * can fail and inserts that are accepted still need to be committed. This would leave the database in a state
     * where it is difficult to determine which inserts have been accepted and which have not. Therefore, we try to
     * commit the pending transaction so any rows that have been reported as inserted will be durable in the database.
     * In terms of ACID properties of the destination database, since committing multiple transactions implies the
     * ETL process is not atomic, at least we can be durable.
     */
    public function __destruct()
    {
        if ($this->size > 0) {
            $this->close();
        } elseif ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    /**
     * Set the commit size.
     *
     * @return $this
     */
    public function size(int $size): Transaction
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Run the given callback inside a transaction or multiple transactions.
     *
     * If run in a single transaction, treat the ETL process as a single atomic transaction and roll back on errors. If
     * run in multiple transactions, the best we can do is provide durability by trying to commit any inserts that are
     * accepted by the destination database.
     *
     * @throws \Exception
     */
    public function run(callable $callback): void
    {
        $this->count++;

        if ($this->shouldBeginTransaction()) {
            $this->beginTransaction();
        }

        try {
            $callback();
        } catch (\Exception $exception) {
            if ($this->pdo->inTransaction()) {
                if (0 === $this->size) {
                    $this->pdo->rollBack();
                } else {
                    $this->pdo->commit();
                }
            }
            throw $exception;
        }

        if ($this->shouldCommit()) {
            $this->commit();
        }
    }

    /**
     * Check if it should begin a new transaction.
     */
    protected function shouldBeginTransaction(): bool
    {
        return !$this->open;
    }

    /**
     * Check if it should commit a transaction.
     */
    protected function shouldCommit(): bool
    {
        return $this->open && ($this->count === $this->size);
    }

    /**
     * Begin a database transaction.
     */
    protected function beginTransaction(): void
    {
        $this->open = true;

        $this->pdo->beginTransaction();
    }

    /**
     * Commit a database transaction.
     */
    protected function commit(): void
    {
        $this->open = false;
        $this->count = 0;

        if ($this->pdo->inTransaction()) {
            $this->pdo->commit();
        }
    }

    /**
     * Commit an open transaction.
     */
    public function close(): void
    {
        if ($this->open) {
            $this->commit();
        }
    }
}

<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Database;

use PDO;

class Transaction
{
    /**
     * The database connection.
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     * Current transaction count.
     *
     * @var int
     */
    protected $count = 0;

    /**
     * Indicates if a transaction is open.
     *
     * @var bool
     */
    protected $open = false;

    /**
     * Commit size.
     *
     * @var int|null
     */
    protected $size;

    /**
     * Create a new Transaction instance.
     *
     * @return void
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Set the commit size.
     *
     * @param int $size
     *
     * @return $this
     */
    public function size(int $size): Transaction
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Run the given callback inside a transaction.
     */
    public function run(callable $callback): void
    {
        $this->count++;

        if ($this->shouldBeginTransaction()) {
            $this->beginTransaction();
        }

        try {
            call_user_func($callback);
        } catch (\Exception $exception) {
            $this->pdo->rollBack();

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
        return !$this->open && (false === is_int($this->size) || 0 === $this->size || 1 === $this->count);
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

        $this->pdo->commit();
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

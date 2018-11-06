<?php

namespace Marquine\Etl\Database;

use PDO;
use Exception;

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
     * @var int
     */
    protected $size;

    /**
     * Create a new Transaction instance.
     *
     * @param  \PDO  $pdo
     * @return void
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Set the commit size.
     *
     * @param  int  $size
     * @return $this
     */
    public function size($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Run the given callback inside a transaction.
     *
     * @param  callbale  $callback
     * @return void
     */
    public function run($callback)
    {
        $this->count++;

        if ($this->shouldBeginTransaction()) {
            $this->beginTransaction();
        }

        try {
            call_user_func($callback);
        } catch (Exception $exception) {
            $this->pdo->rollBack();

            throw $exception;
        }

        if ($this->shouldCommit()) {
            $this->commit();
        }
    }

    /**
     * Check if it should begin a new transaction.
     *
     * @return bool
     */
    protected function shouldBeginTransaction()
    {
        return ! $this->open && (empty($this->size) || $this->count === 1);
    }

    /**
     * Check if it should commit a transaction.
     *
     * @return bool
     */
    protected function shouldCommit()
    {
        return $this->open && ($this->count === $this->size);
    }

    /**
     * Begin a database transaction.
     *
     * @return void
     */
    protected function beginTransaction()
    {
        $this->open = true;

        $this->pdo->beginTransaction();
    }

    /**
     * Commit a database transaction.
     *
     * @return void
     */
    protected function commit()
    {
        $this->open = false;
        $this->count = 0;

        $this->pdo->commit();
    }

    /**
     * Commit an open transaction.
     *
     * @return void
     */
    public function close()
    {
        if ($this->open) {
            $this->commit();
        }
    }
}

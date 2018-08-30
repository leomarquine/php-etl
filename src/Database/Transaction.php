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
     * Run the given callback.
     *
     * @param  \stdClass  $metadata
     * @param  callbale  $callback
     * @return void
     */
    public function run($metadata, $callback)
    {
        if ($this->shouldBeginTransaction($metadata)) {
            $this->pdo->beginTransaction();
        }

        try {
            call_user_func($callback);
        } catch (Exception $exception) {
            $this->pdo->rollBack();

            throw $exception;
        }

        if ($this->shouldCommit($metadata)) {
            $this->pdo->commit();
        }
    }

    /**
     * Check if it should begin a new transaction.
     *
     * @param  \stdClass  $metadata
     * @return bool
     */
    protected function shouldBeginTransaction($metadata)
    {
        if (empty($this->size) && $metadata->current == 1) {
            return true;
        }

        if (!empty($this->size) && ($metadata->current - 1) % $this->size == 0) {
            return true;
        }

        return false;
    }

    /**
     * Check if it should commit a transaction.
     *
     * @param  \stdClass  $metadata
     * @return bool
     */
    protected function shouldCommit($metadata)
    {
        if (empty($this->size) && $metadata->current == $metadata->total) {
            return true;
        }

        if (!empty($this->size) && (($metadata->current - 1) % $this->size == $this->size - 1 || $metadata->current == $metadata->total)) {
            return true;
        }

        return false;
    }
}

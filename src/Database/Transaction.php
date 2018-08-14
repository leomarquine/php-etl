<?php

namespace Marquine\Etl\Database;

use Exception;

class Transaction
{
    /**
    * The database connection.
    *
    * @var \Marquine\Etl\Database\Connection
    */
    protected $connection;

    /**
     * Commit size.
     *
     * @var int
     */
    protected $size;

    /**
    * Create a new Transaction instance.
    *
    * @param  \Marquine\Etl\Database\Connection  $connection
    * @return void
    */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
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
     * @param  callbale  $callback
     * @param  \stdClass  $metadata
     * @return void
     */
    public function run($callback, $metadata)
    {
        if ($this->shouldBeginTransaction($metadata)) {
            $this->connection->beginTransaction();
        }

        try {
            call_user_func($callback);
        } catch (Exception $exception) {
            $this->connection->rollBack();

            throw $exception;
        }

        if ($this->shouldCommit($metadata)) {
            $this->connection->commit();
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

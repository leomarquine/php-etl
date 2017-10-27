<?php

namespace Marquine\Etl\Database;

use Exception;
use InvalidArgumentException;

class Transaction
{
    /**
    * The database connection.
    *
    * @var \Marquine\Etl\Database\Connection
    */
    protected $connection;

    /**
     * The transaction data.
     *
     * @var mixed
     */
    protected $data;

    /**
    * The transaction mode.
    *
    * @var string|int
    */
    protected $mode;

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
     * Get a transaction instance for the given connection.
     *
     * @param  string  $connection
     * @return static
     */
    public static function connection($connection)
    {
        return new static(Manager::connection($connection));
    }

    /**
     * Set the transaction mode.
     *
     * @param  string|int  $mode
     * @return $this
     */
    public function mode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Set the transactoin data.
     *
     * @param  mixed  $data
     * @return $this
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the transaction.
     *
     * @param  callable  $callback
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function run($callback)
    {
        if ($this->mode > 0) {
            return $this->multiple($callback);
        }

        if ($this->mode == 'single') {
            return $this->single($callback);
        }

        if ($this->mode == 'none') {
            return $this->none($callback);
        }

        throw new InvalidArgumentException('The specified transaction mode is not valid.');
    }

    /**
    * Execute queries in multiple transactions.
    *
    * @param  callable  $callback
    * @return array
    */
    protected function multiple($callback)
    {
        $count = 0;

        $results = [];

        foreach ($this->data as $row) {
            if ($count % $this->mode == 0) {
                $this->connection->beginTransaction();
            }

            try {
                if ($result = $callback($row)) {
                    $results[] = $result;
                }
            } catch (Exception $e) {
                $this->connection->rollBack();

                throw $e;
            }

            if ($count % $this->mode == $this->mode - 1) {
                $this->connection->commit();
            }

            $count++;
        }

        if ($count % $this->mode != 0) {
            $this->connection->commit();
        }

        return $results;
    }

    /**
    * Execute queries in a single transaction.
    *
    * @param  callable  $callback
    * @return array
    */
    protected function single($callback)
    {
        $results = [];

        $this->connection->beginTransaction();

        foreach ($this->data as $row) {
            try {
                if ($result = $callback($row)) {
                    $results[] = $result;
                }
            } catch (Exception $e) {
                $this->connection->rollBack();

                throw $e;
            }
        }

        $this->connection->commit();

        return $results;
    }

    /**
    * Execute queries with no transaction.
    *
    * @param  callable  $callback
    * @return array
    */
    protected function none($callback)
    {
        $results = [];

        foreach ($this->data as $row) {
            if ($result = $callback($row)) {
                $results[] = $result;
            }
        }

        return $results;
    }
}

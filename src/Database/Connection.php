<?php

namespace Marquine\Etl\Database;

class Connection
{
    /**
    * PDO connection.
    *
    * @var \PDO
    */
    protected $pdo;

    /**
    * Create a new Connection instance.
    *
    * @param  \PDO  $pdo
    * @return void
    */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get a new query builder instance.
     *
     * @return \Marquine\Etl\Database\Query
     */
    public function query()
    {
        return new Query($this);
    }

    /**
     * Get a new statement builder instance.
     *
     * @return \Marquine\Etl\Database\Statement
     */
    public function statement()
    {
        return new Statement($this);
    }

    /**
     * Get a new transaction instance.
     *
     * @return \Marquine\Etl\Database\Transaction
     */
    public function transaction($mode)
    {
        $transaction = new Transaction($this);

        return $transaction->mode($mode);
    }

    /**
     * Dynamically pass method calls to the PDO instance.
     *
     * @param  string  $method
     * @param  array  $arguments
     * @return \PDO
     */
    public function __call($method, $arguments)
    {
        return $this->pdo->{$method}(...$arguments);
    }
}

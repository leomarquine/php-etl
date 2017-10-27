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

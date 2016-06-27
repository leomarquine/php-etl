<?php

namespace Marquine\Metis\Database\Connection;

use PDO;
use Exception;

abstract class Connection
{
    /**
    * The database connection.
    *
    * @var PDO
    */
    protected $connection;

    /**
     * The default PDO connection options.
     *
     * @var array
     */
    protected $options = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    /**
    * Connect to a database.
    *
    * @param  string $dsn
    * @param  array  $config
    * @param  array  $options
    * @return void
    */
    protected function connect($dsn, $config, $options)
    {
        $username = isset($config['username']) ? $config['username'] : null;

        $password = isset($config['password']) ? $config['password'] : null;

        $this->connection = new PDO($dsn, $username, $password, $options);
    }

    /**
     * Get the PDO options based on the configuration.
     *
     * @param  array $config
     * @return array
     */
    public function getOptions(array $config)
    {
        $options = isset($config['options']) ? $config['options'] : [];

        return array_diff_key($this->options, $options) + $options;
    }

    /**
    * Execute a statement.
    *
    * @param  string $statement
    * @return void
    */
    public function exec($statement)
    {
        $this->connection->exec($statement);
    }

    /**
    * Prepare a statement.
    *
    * @param  string $statement
    * @return \PDOStatement
    */
    public function prepare($statement)
    {
        return $this->connection->prepare($statement);
    }

    /**
    * Execute a custom query.
    *
    * @param  string $query
    * @param  array  $bindings
    * @return array
    */
    public function query($query, $bindings)
    {
        $query = $this->prepare($query);

        $query->execute($bindings);

        return $query->fetchAll();
    }

    /**
    * Execute a select statement.
    *
    * @param  string $table
    * @param  mixed  $columns
    * @param  array  $where
    * @return void
    */
    public function select($table, $columns = '*', $where = [])
    {
        if (! $columns) {
            $columns = ['*'];
        }

        if (is_string($columns)) {
            $columns = [$columns];
        }

        $columns = implode(', ', $columns);

        if (empty($where)) {
            $statement = $this->prepare("select $columns from $table");
        } else {
            $conditions = implode(' and ', array_map(function($column) { return "$column = :$column"; }, array_keys($where)));

            $statement = $this->prepare("select $columns from $table where $conditions");
        }

        $statement->execute($where);

        return $statement->fetchAll();
    }

    /**
    * Execute insert statements.
    *
    * @param  string $table
    * @param  array  $items
    * @param  mixed  $transaction
    * @return void
    */
    public function insert($table, $items, $transaction = 100)
    {
        if (empty($items)) {
            return;
        }

        $columns = implode(', ', array_keys(reset($items)));

        $values = implode(', ', array_map(function($column) { return ":$column"; }, array_keys(reset($items))));

        $statement = $this->prepare("insert into $table ($columns) values ($values)");

        $callback = function ($items) use ($statement) {
            foreach ($items as $item) {
                $statement->execute($item);
            }
        };

        $this->transaction($items, $callback, $transaction);
    }

    /**
    * Execute update statements.
    *
    * @param  string $table
    * @param  array  $items
    * @param  array  $keys
    * @param  mixed  $transaction
    * @return void
    */
    public function update($table, $items, $keys, $transaction)
    {
        if (empty($items)) {
            return;
        }

        $columns = implode(', ', array_map(function($column) { return "$column = :$column"; }, array_keys(reset($items))));

        $conditions = implode(' and ', array_map(function($column) { return "$column = :$column"; }, $keys));

        $statement = $this->prepare("update $table set $columns where $conditions");

        $callback = function ($items) use ($statement) {
            foreach ($items as $item) {
                $statement->execute($item);
            }
        };

        $this->transaction($items, $callback, $transaction);
    }

    /**
    * Execute delete statements.
    *
    * @param  string $table
    * @param  array  $items
    * @param  array  $keys
    * @param  mixed  $transaction
    * @return void
    */
    public function delete($table, $items, $keys, $transaction)
    {
        if (empty($items)) {
            return;
        }

        $conditions = implode(' and ', array_map(function($column) { return "$column = :$column"; }, $keys));

        $statement = $this->prepare("delete from $table where $conditions");

        $callback = function ($items) use ($statement, $keys) {
            $keys = array_flip($keys);

            foreach ($items as $item) {
                $bindings = array_intersect_key($item, $keys);

                $statement->execute($bindings);
            }
        };

        $this->transaction($items, $callback, $transaction);
    }

    /**
    * Set 'deleted_at' column to current datetime.
    *
    * @param  string $table
    * @param  array  $items
    * @param  array  $keys
    * @param  string $time
    * @param  mixed  $transaction
    * @return void
    */
    public function softDelete($table, $items, $keys, $time, $transaction)
    {
        if (empty($items)) {
            return;
        }

        $conditions = implode(' and ', array_map(function($column) { return "$column = :$column"; }, $keys));

        $statement = $this->prepare("update $table set deleted_at = :deleted_at where $conditions");

        $callback = function ($items) use ($statement, $keys, $time) {
            $keys = array_flip($keys);

            foreach ($items as $item) {
                $bindings = array_intersect_key($item, $keys);
                $bindings['deleted_at'] = $time;

                $statement->execute($bindings);
            }
        };

        $this->transaction($items, $callback, $transaction);
    }

    /**
    * Perform a database transaction.
    *
    * @param  array    $items
    * @param  callable $callback
    * @param  mixed    $transaction
    * @return void
    */
    protected function transaction($items, $callback, $transaction)
    {
        if (! $transaction) {
            return call_user_func($callback, $items);
        }

        $chunks = array_chunk($items, $transaction);

        foreach ($chunks as $chunk) {
            $this->connection->beginTransaction();

            try {
                call_user_func($callback, $chunk);

                $this->connection->commit();
            } catch (Exception $e) {
                $this->connection->rollBack();

                throw $e;
            }
        }
    }
}

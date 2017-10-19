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
    * @param \PDO $pdo
    * @return void
    */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
    * Execute a select query.
    *
    * @param  string  $table
    * @param  array|null  $columns
    * @param  array|null  $where
    * @return \PDOStatement
    */
    public function select($table, $columns = null, $where = null)
    {
        $columns = $columns ? implode(', ', $columns) : '*';

        $sql = "select {$columns} from {$table}";

        if ($where) {
            $conditions = $this->bindings(array_keys($where), '{column} = :{column}', ' and ');

            $sql .= " where {$conditions}";
        }

        $statement = $this->pdo->prepare($sql);

        $statement->execute($where);

        return $statement;
    }

    /**
    * Prepare a select statement.
    *
    * @param string $table
    * @param array $columns
    * @return \PDOStatement
    */
    public function prepareInsert($table, $columns)
    {
        $values = $this->bindings($columns, ':{column}', ', ');

        $columns = implode(', ', $columns);

        $sql = "insert into {$table} ({$columns}) values ({$values})";

        return $this->pdo->prepare($sql);
    }

    /**
    * Prepare an update statement.
    *
    * @param string $table
    * @param array $columns
    * @param array $keys
    * @return \PDOStatement
    */
    public function prepareUpdate($table, $columns, $keys)
    {
        $columns = $this->bindings($columns, '{column} = :{column}', ', ');

        $where = $this->bindings($keys, '{column} = :{column}', ' and ');

        $sql = "update {$table} set {$columns} where {$where}";

        return $this->pdo->prepare($sql);
    }

    /**
    * Prepare a delete statement.
    *
    * @param string $table
    * @param array $keys
    * @return \PDOStatement
    */
    public function prepareDelete($table, $keys)
    {
        $where = $this->bindings($keys, '{column} = :{column}', ' and ');

        $sql = "delete from {$table} where {$where}";

        return $this->pdo->prepare($sql);
    }

    /**
    * Perform a database transaction.
    *
    * @param array $items
    * @param callable $callback
    * @param int $size
    * @return void
    */
    public function transaction($items, $callback, $size)
    {
        if (! $size) {
            return call_user_func($callback, $items);
        }

        $chunks = array_chunk($items, $size, true);

        foreach ($chunks as $chunk) {
            $this->pdo->beginTransaction();

            try {
                call_user_func($callback, $chunk);

                $this->pdo->commit();
            } catch (Exception $e) {
                $this->pdo->rollBack();

                throw $e;
            }
        }
    }

    /**
    * Make columns bindings.
    *
    * @param array $columns
    * @param string $mask
    * @param string $separator
    * @return string
    */
    protected function bindings($columns, $mask, $separator)
    {
        $columns = array_map(function ($column) use ($mask) {
            return str_replace('{column}', $column, $mask);
        }, $columns);

        return implode($separator, $columns);
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

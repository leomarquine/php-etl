<?php

namespace Marquine\Metis\Database;

abstract class Connection
{
    protected $pdo;

    protected $table;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function exec($statement)
    {
        return $this->pdo->exec($statement);
    }

    public function prepare($statement)
    {
        return $this->pdo->prepare($statement);
    }

    public function table($table)
    {
        $this->table = $table;

        return $this;
    }

    public function select($columns = null, $where = [])
    {
        $columns = $columns ? implode(', ', $columns) : '*';

        $sql = "select {$columns} from {$this->table}";

        if (! empty($where)) {
            $conditions = $this->bindings(array_keys($where), '{column} = :{column}', ' and ');

            $sql .= " where {$conditions}";
        }

        $statement = $this->pdo->prepare($sql);

        $statement->execute($where);

        return $statement->fetchAll();
    }

    public function prepareInsert($columns)
    {
        $values = $this->bindings($columns, ':{column}', ', ');

        $columns = implode(', ', $columns);

        $sql = "insert into {$this->table} ({$columns}) values ({$values})";

        return $this->pdo->prepare($sql);
    }

    public function prepareUpdate($columns, $keys)
    {
        $columns = $this->bindings($columns, '{column} = :{column}', ', ');

        $where = $this->bindings($keys, '{column} = :{column}', ' and ');

        $sql = "update {$this->table} set {$columns} where {$where}";

        return $this->pdo->prepare($sql);
    }

    public function prepareDelete($keys)
    {
        $where = $this->bindings($keys, '{column} = :{column}', ' and ');

        $sql = "delete from {$this->table} where {$where}";

        return $this->pdo->prepare($sql);
    }

    /**
    * Perform a database transaction.
    *
    * @param  array    $items
    * @param  callable $callback
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

    protected function bindings($columns, $mask, $separator)
    {
        $columns = array_map(function ($column) use ($mask) {
            return str_replace('{column}', $column, $mask);
        }, $columns);

        return implode($separator, $columns);
    }
}

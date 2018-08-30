<?php

namespace Marquine\Etl\Database;

use PDO;

class Query
{
    /**
     * The database connection.
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     * The bindings for the query.
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * The sql query components.
     *
     * @var array
     */
    protected $query = [];

    /**
     * The where constraints for the query.
     *
     * @var array
     */
    protected $wheres = [];

    /**
     * Create a new Query instance.
     *
     * @param  \PDO  $pdo
     * @return void
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Execute the query.
     *
     * @return \PDOStatement
     */
    public function execute()
    {
        $statement = $this->pdo->prepare($this->toSql());

        $statement->execute($this->bindings);

        return $statement;
    }

    /**
     * Get the sql query string.
     *
     * @return string
     */
    public function toSql()
    {
        $this->compileWheres();

        return implode(' ', $this->query);
    }

    /**
     * Get the query bindings.
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * Select statement.
     *
     * @param  string  $table
     * @param  array  $columns
     * @return $this
     */
    public function select($table, $columns = ['*'])
    {
        $columns = $this->implode($columns);

        $this->query[] = "select $columns from $table";

        return $this;
    }

    /**
     * Insert statement.
     *
     * @param  string  $table
     * @param  array  $columns
     * @return $this
     */
    public function insert($table, $columns)
    {
        $this->bindings = array_merge($this->bindings, array_values($columns));

        $values = $this->implode($columns, '?');

        $columns = $this->implode(array_keys($columns));

        $this->query[] = "insert into $table ($columns) values ($values)";

        return $this;
    }

    /**
     * Update statement.
     *
     * @param  string  $table
     * @param  array  $columns
     * @return $this
     */
    public function update($table, $columns)
    {
        $this->bindings = array_merge($this->bindings, array_values($columns));

        $columns = $this->implode(array_keys($columns), '{column} = ?');

        $this->query[] = "update $table set $columns";

        return $this;
    }

    /**
     * Delete statement.
     *
     * @param  string  $table
     * @return $this
     */
    public function delete($table)
    {
        $this->query[] = "delete from {$table}";

        return $this;
    }

    /**
     * Where statement.
     *
     * @param  array  $columns
     * @return $this
     */
    public function where($columns)
    {
        foreach ($columns as $column => $value) {
            $this->wheres[] = [
                'type' => 'Where', 'column' => $column, 'value' => $value, 'operator' => '=', 'boolean' => 'and',
            ];
        }

        return $this;
    }

    /**
     * Where In statement.
     *
     * @param  array|string  $column
     * @param  array  $values
     * @param  string  $operator
     * @return $this
     */
    public function whereIn($column, $values, $operator = 'in')
    {
        if (is_string($column)) {
            $this->wheres[] = ['type' => 'WhereIn', 'column' => $column, 'values' => $values, 'operator' => $operator, 'boolean' => 'and'];
        } else {
            $this->wheres[] = ['type' => 'CompositeWhereIn', 'columns' => $column, 'values' => $values, 'operator' => $operator, 'boolean' => 'and'];
        }

        return $this;
    }

    /**
     * Where Not In statement.
     *
     * @param  array|string  $column
     * @param  array  $values
     * @return $this
     */
    public function whereNotIn($column, $values)
    {
        return $this->whereIn($column, $values, 'not in');
    }

    /**
     * Compile all where statements.
     *
     * @return void
     */
    protected function compileWheres()
    {
        if (empty($this->wheres)) {
            return;
        }

        $this->query[] = 'where';

        foreach ($this->wheres as $index => $condition) {
            $method = 'compile'.$condition['type'];

            if ($index == 0) {
                $condition['boolean'] = '';
            }

            $this->query[] = trim($this->{$method}($condition));
        }
    }

    /**
     * Compile the basic where statement.
     *
     * @param  array  $where
     * @return string
     */
    protected function compileWhere($where)
    {
        extract($where);

        $this->bindings[] = $value;

        return "$boolean $column $operator ?";
    }

    /**
     * Compile the where in statement.
     *
     * @param  array  $where
     * @return string
     */
    protected function compileWhereIn($where)
    {
        extract($where);

        $this->bindings = array_merge($this->bindings, $values);

        $parameters = $this->implode($values, '?');

        return "$boolean $column $operator ($parameters)";
    }

    /**
     * Compile the composite where in statement.
     *
     * @param  array  $where
     * @return string
     */
    protected function compileCompositeWhereIn($where)
    {
        extract($where);

        sort($columns);

        $parameters = [];

        foreach ($values as $value) {
            ksort($value);

            $this->bindings = array_merge($this->bindings, array_values($value));

            $parameters[] = "({$this->implode($value, '?')})";
        }

        $parameters = $this->implode($parameters);

        $columns = $this->implode($columns);

        return "$boolean ($columns) $operator ($parameters)";
    }

    /**
     * Join array elements using a string mask.
     *
     * @param  array  $columns
     * @param  string  $mask
     * @return string
     */
    protected function implode($columns, $mask = '{column}')
    {
        $columns = array_map(function ($column) use ($mask) {
            return str_replace('{column}', $column, $mask);
        }, $columns);

        return implode(', ', $columns);
    }
}

<?php

namespace Marquine\Etl\Database;

use PDO;

class Statement
{
    /**
     * The database connection.
     *
     * @var \PDO
     */
    protected $pdo;

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
     * Create a new Statement instance.
     *
     * @param  \PDO  $pdo
     * @return void
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Prepare the statement for execution.
     *
     * @return \PDOStatement
     */
    public function prepare()
    {
        return $this->pdo->prepare($this->toSql());
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
        $values = $this->implode($columns, ':{column}');

        $columns = $this->implode($columns);

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
        $columns = $this->implode($columns, '{column} = :{column}');

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
        $this->query[] = "delete from $table";

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
        foreach ($columns as $column) {
            $this->wheres[] = [
                'type' => 'Where', 'column' => $column, 'operator' => '=', 'boolean' => 'and',
            ];
        }

        return $this;
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

        return "$boolean $column $operator :$column";
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

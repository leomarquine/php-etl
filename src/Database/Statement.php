<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Database;

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
     * @return void
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Prepare the statement for execution.
     */
    public function prepare(): \PDOStatement
    {
        return $this->pdo->prepare($this->toSql());
    }

    /**
     * Get the sql query string.
     */
    public function toSql(): string
    {
        $this->compileWheres();

        return implode(' ', $this->query);
    }

    /**
     * Select statement.
     *
     * @return $this
     */
    public function select(string $table, array $columns = ['*']): Statement
    {
        $columns = $this->implode($columns);

        $this->query[] = "select $columns from $table";

        return $this;
    }

    /**
     * Insert statement.
     *
     * @return $this
     */
    public function insert(string $table, array $columns): Statement
    {
        $values = $this->implode($columns, ':{column}');

        $columns = $this->implode($columns);

        $this->query[] = "insert into $table ($columns) values ($values)";

        return $this;
    }

    /**
     * Update statement.
     *
     * @return $this
     */
    public function update(string $table, array $columns): Statement
    {
        $columns = $this->implode($columns, '{column} = :{column}');

        $this->query[] = "update $table set $columns";

        return $this;
    }

    /**
     * Delete statement.
     *
     * @return $this
     */
    public function delete(string $table): Statement
    {
        $this->query[] = "delete from $table";

        return $this;
    }

    /**
     * Where statement.
     *
     * @return $this
     */
    public function where(array $columns): Statement
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
     */
    protected function compileWheres(): void
    {
        if ([] === $this->wheres) {
            return;
        }

        $this->query[] = 'where';

        foreach ($this->wheres as $index => $condition) {
            $method = 'compile' . $condition['type'];

            if (0 == $index) {
                $condition['boolean'] = '';
            }

            $this->query[] = trim($this->{$method}($condition));
        }
    }

    /**
     * Compile the basic where statement.
     *
     * @return string
     */
    protected function compileWhere(array $where)
    {
        // This code is here to remove the use of the extract() method in the original repo. See the git history.
        $boolean = array_key_exists('boolean', $where) ? $where['boolean'] : null;
        $column = array_key_exists('column', $where) ? $where['column'] : null;
        $operator = array_key_exists('operator', $where) ? $where['operator'] : null;

        return "$boolean $column $operator :$column";
    }

    /**
     * Join array elements using a string mask.
     */
    protected function implode(array $columns, string $mask = '{column}'): string
    {
        $columns = array_map(function ($column) use ($mask): string {
            return str_replace('{column}', $column, $mask);
        }, $columns);

        return implode(', ', $columns);
    }
}

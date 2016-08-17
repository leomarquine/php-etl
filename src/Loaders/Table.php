<?php

namespace Marquine\Etl\Loaders;

use Marquine\Etl\Etl;
use Marquine\Etl\Traits\Database;
use Marquine\Etl\Traits\Indexable;

class Table implements LoaderInterface
{
    use Indexable, Database;

    /**
     * The connection name.
     *
     * @var string
     */
    public $connection = 'default';

    /**
     * The primary key or identifier of the table.
     *
     * @var array
     */
    public $keys = ['id'];

    /**
     * Indicates if the loader will insert data.
     *
     * @var bool
     */
    public $insert = true;

    /**
     * Indicates if the loader will update data.
     *
     * @var bool
     */
    public $update = true;

    /**
     * Indicates if the loader will delete/softdelete data.
     *
     * @var bool
     */
    public $delete = false;

    /**
     * Indicates if the loader will execute queries regardless of current table data.
     *
     * @var bool
     */
    public $skipDataCheck = false;

    /**
     * Indicates if the loader will not compare source and destination data before update.
     *
     * @var bool
     */
    public $forceUpdate = false;

    /**
     * Indicates if the loader will fill 'created_at' and 'updated_at' timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Transaction size.
     *
     * @var mixed
     */
    protected $transaction = 100;

    /**
     * The table name.
     *
     * @var string
     */
    protected $table;

    /**
     * Datetime to use in timestamps columns.
     *
     * @var string
     */
    protected $time;

    /**
     * Columns to be loaded.
     *
     * @var mixed
     */
    protected $columns;

    /**
     * Load data to the given destination.
     *
     * @param string $table
     * @param array $items
     * @return void
     */
    public function load($table, $items)
    {
        $this->connect($this->connection);

        $this->table = $table;

        $this->time = date('Y-m-d G:i:s');

        $this->setColumns($items);

        $this->normalizeKeys();

        $old = [];

        if (! $this->skipDataCheck) {
            $select = $this->db->select($this->table);

            $old = $this->index($select, $this->keys);

            $items = $this->index($items, $this->keys);
        }

        if ($this->insert === true) {
            $this->insert(array_diff_key($items, $old));
        }

        if ($this->update === true) {
            $this->update(array_intersect_key($items, $old), array_intersect_key($old, $items));
        }

        if ($this->delete === true) {
            $this->delete(array_diff_key($old, $items));
        }

        if ($this->delete === 'soft') {
            $this->softDelete(array_diff_key($old, $items));
        }
    }

    /**
     * Insert data.
     *
     * @param array $items
     * @return void
     */
    protected function insert($items)
    {
        if (empty($items)) {
            return;
        }

        $statement = $this->db->prepareInsert($this->table, $this->columns);

        $callback = function ($items) use ($statement) {
            foreach ($items as $item) {
                if ($this->timestamps) {
                    $item['created_at'] = $this->time;
                    $item['updated_at'] = $this->time;
                }

                $statement->execute($item);
            }
        };

        $this->db->transaction($items, $callback, $this->transaction);
    }

    /**
     * Update data.
     *
     * @param array $new
     * @param array $old
     * @return void
     */
    protected function update($items, $old)
    {
        if (empty($items)) {
            return;
        }

        $statement = $this->db->prepareUpdate($this->table, $this->columns, $this->keys);

        $callback = function($items) use ($statement, $old) {
            foreach ($items as $key => $item) {
                if ($this->forceUpdate || $this->needsUpdate($item, $old[$key])) {
                    if ($this->timestamps) {
                        $item['created_at'] = $old[$key]['created_at'];
                        $item['updated_at'] = $this->time;
                    }

                    if ($this->delete === 'soft') {
                        $item['deleted_at'] = null;
                    }

                    $statement->execute($item);
                }
            }
        };

        $this->db->transaction($items, $callback, $this->transaction);
    }

    /**
     * Delete data.
     *
     * @param array $items
     * @return void
     */
    protected function delete($items)
    {
        if (empty($items)) {
            return;
        }

        $statement = $this->db->prepareDelete($this->table, $this->keys);

        $callback = function ($items) use ($statement) {
            foreach ($items as $item) {
                $statement->execute(array_intersect_key($item, $this->keys));
            }
        };

        $this->db->transaction($items, $callback, $this->transaction);
    }

    /**
     * Soft delete data.
     *
     * @param array $items
     * @return void
     */
    protected function softDelete($items)
    {
        if (empty($items)) {
            return;
        }

        $statement = $this->db->prepareUpdate($this->table, $this->columns, $this->keys);

        $callback = function ($items) use ($statement) {
            foreach ($items as $item) {
                $params = array_merge(['deleted_at' => $this->time], array_intersect_key($item, $this->keys));

                $statement->execute($params);
            }
        };

        $this->db->transaction($items, $callback, $this->transaction);
    }

    /**
     * Check if a row needs update.
     *
     * @param array $new
     * @param array $old
     * @return bool
     */
    protected function needsUpdate($new, $old)
    {
        if (isset($old['deleted_at']) && $old['deleted_at']) {
            return true;
        }

        unset($old['created_at'], $old['updated_at']);

        return ! empty(array_diff($new, $old));
    }

    /**
     * Normalize keys.
     *
     * @return void
     */
    protected function normalizeKeys()
    {
        if (is_string($this->keys)) {
            $this->keys = [$this->keys];
        }

        $keys = [];

        foreach ($this->keys as $key) {
            $keys[$key] = $key;
        }

        $this->keys = $keys;
    }

    /**
     * Set the loader columns.
     *
     * @param array $items
     * @return void
     */
    protected function setColumns($items)
    {
        $item = reset($items);

        $this->columns = $item ? array_keys($item) : [];

        if ($this->timestamps) {
            $this->columns[] = 'created_at';
            $this->columns[] = 'updated_at';
        }

        if ($this->delete === 'soft') {
            $this->columns[] = 'deleted_at';
        }
    }
}

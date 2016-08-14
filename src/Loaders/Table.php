<?php

namespace Marquine\Metis\Loaders;

use Marquine\Metis\Metis;
use Marquine\Metis\Contracts\Loader;
use Marquine\Metis\Traits\Indexable;
use Marquine\Metis\Traits\SetOptions;

class Table implements Loader
{
    use Indexable, SetOptions;

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
     * The connection name.
     *
     * @var string
     */
    protected $connection = 'default';

    /**
     * The primary key or identifier of the table.
     *
     * @var array
     */
    protected $keys = ['id'];

    /**
     * Indicates if the loader will insert data.
     *
     * @var bool
     */
    protected $insert = true;

    /**
     * Indicates if the loader will update data.
     *
     * @var bool
     */
    protected $update = true;

    /**
     * Indicates if the loader will delete/softdelete data.
     *
     * @var bool
     */
    protected $delete = false;

    /**
     * Indicates if the loader will execute queries regardless of current table data.
     *
     * @var bool
     */
    protected $skipDataCheck = false;

    /**
     * Indicates if the loader will not compare source and destination data before update.
     *
     * @var bool
     */
    protected $forceUpdate = false;

    /**
     * Indicates if the loader will fill 'created_at' and 'updated_at' timestamps.
     *
     * @var bool
     */
    protected $timestamps = false;

    /**
     * Transaction size.
     *
     * @var mixed
     */
    protected $transaction = 100;

    protected $columns;

    protected $database;

    /**
     * Load data to the given destination.
     *
     * @param  string $table
     * @param  array  $items
     * @return void
     */
    public function load($table, $items)
    {
        $this->database = Metis::connection($this->connection);

        $this->table = $table;

        $this->columns = $this->columns($items);

        $this->time = date('Y-m-d G:i:s');

        $this->normalizeKeys();

        $old = [];

        if (! $this->skipDataCheck) {
            $select = $this->database->table($this->table)->select();

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
     * @param  array $items
     * @return void
     */
    protected function insert($items)
    {
        $insert = $this->database->table($this->table)->prepareInsert($this->columns);

        $callback = function ($items) use ($insert) {
            foreach ($items as $item) {
                if ($this->timestamps) {
                    $item['created_at'] = $this->time;
                    $item['updated_at'] = $this->time;
                }

                $insert->execute(array_values($item));
            }
        };

        $this->database->transaction($items, $callback, $this->transaction);
    }

    /**
     * Update data.
     *
     * @param  array $new
     * @param  array $old
     * @return void
     */
    protected function update($items, $old)
    {
        $update = $this->database->table($this->table)->prepareUpdate($this->columns, $this->keys);

        $callback = function($items) use ($update, $old) {
            foreach ($items as $key => $item) {
                if ($this->forceUpdate || $this->needsUpdate($item, $old[$key])) {
                    if ($this->timestamps) {
                        $item['created_at'] = $old[$key]['created_at'];
                        $item['updated_at'] = $this->time;
                    }

                    if ($this->delete === 'soft') {
                        $item['deleted_at'] = null;
                    }

                    $update->execute($item);
                }
            }
        };

        $this->database->transaction($items, $callback, $this->transaction);
    }

    /**
     * Delete data.
     *
     * @param  array $items
     * @return void
     */
    protected function delete($items)
    {
        $delete = $this->database->table($this->table)->prepareDelete($this->keys);

        $callback = function ($items) use ($delete) {
            foreach ($items as $item) {
                $delete->execute(array_intersect_key($item, $this->keys));
            }
        };

        $this->database->transaction($items, $callback, $this->transaction);
    }

    /**
     * Soft delete data.
     *
     * @param  array $items
     * @return void
     */
    protected function softDelete($items)
    {
        $update = $this->database->table($this->table)->prepareUpdate($this->columns, $this->keys);

        $callback = function ($items) use ($update) {
            foreach ($items as $item) {
                $params = array_merge(['deleted_at' => $this->time], array_intersect_key($item, $this->keys));

                $update->execute($params);
            }
        };

        $this->database->transaction($items, $callback, $this->transaction);
    }

    /**
     * Check if a row needs update.
     *
     * @param  array $new
     * @param  array $old
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

    protected function columns($items)
    {
        $item = reset($items);

        $columns = $item ? array_keys($item) : [];

        if ($this->timestamps) {
            $columns[] = 'created_at';
            $columns[] = 'updated_at';
        }

        if ($this->delete === 'soft') {
            $columns[] = 'deleted_at';
        }

        return $columns;
    }
}

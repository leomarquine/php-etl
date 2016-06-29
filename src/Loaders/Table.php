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

    /**
     * Load data to the given destination.
     *
     * @param  string $table
     * @param  array  $items
     * @return void
     */
    public function load($table, $items)
    {
        $this->table = $table;

        $this->time = date('Y-m-d G:i:s');

        $this->normalizeKeys();

        $old = [];

        if (! $this->skipDataCheck) {
            $select = Metis::connection($this->connection)->fetchAll(
                "select * from {$this->table}"
            );

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
        $callback = function($items) {
            foreach ($items as $item) {
                if ($this->timestamps) {
                    $item['created_at'] = $this->time;
                    $item['updated_at'] = $this->time;
                }

                Metis::connection($this->connection)->insert($this->table, $item);
            }
        };

        $this->transaction($items, $callback);
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
        $callback = function($items) use ($old) {
            foreach ($items as $key => $item) {
                if ($this->forceUpdate || $this->needsUpdate($item, $old[$key])) {
                    if ($this->timestamps) {
                        $item['updated_at'] = $this->time;
                    }

                    if ($this->delete === 'soft') {
                        $item['deleted_at'] = null;
                    }

                    Metis::connection($this->connection)->update(
                        $this->table, $item, array_intersect_key($item, $this->keys)
                    );
                }
            }
        };

        $this->transaction($items, $callback);
    }

    /**
     * Delete data.
     *
     * @param  array $items
     * @return void
     */
    protected function delete($items)
    {
        $callback = function ($items) {
            foreach ($items as $item) {
                Metis::connection($this->connection)->delete(
                    $this->table, array_intersect_key($item, $this->keys)
                );
            }
        };

        $this->transaction($items, $callback);
    }

    /**
     * Soft delete data.
     *
     * @param  array $items
     * @return void
     */
    protected function softDelete($items)
    {
        $callback = function ($items) {
            foreach ($items as $item) {
                Metis::connection($this->connection)->update(
                    $this->table, ['deleted_at' => $this->time], array_intersect_key($item, $this->keys)
                );
            }
        };

        $this->transaction($items, $callback);
    }

    /**
    * Perform a database transaction.
    *
    * @param  array    $items
    * @param  callable $callback
    * @return void
    */
    protected function transaction($items, $callback)
    {
        if (! $transaction) {
            return call_user_func($callback, $items);
        }

        $chunks = array_chunk($items, $this->transaction);

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
}

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

        if (is_string($this->keys)) {
            $this->keys = [$this->keys];
        }

        $old = [];

        if (! $this->skipDataCheck) {
            $old = $this->index(Metis::db($this->connection)->select($this->table), $this->keys);
            $items = $this->index($items, $this->keys);
        }

        if ($this->insert) {
            $this->insert(array_diff_key($items, $old));
        }

        if ($this->update) {
            $this->update(array_intersect_key($items, $old), array_intersect_key($old, $items));
        }

        if ($this->delete) {
            $this->delete(array_diff_key($old, $items));
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
        if ($this->timestamps) {
            foreach ($items as &$item) {
                $item['created_at'] = $this->time;
                $item['updated_at'] = $this->time;
            }
        }

        Metis::db($this->connection)->insert($this->table, $items, $this->transaction);
    }

    /**
     * Update data.
     *
     * @param  array $new
     * @param  array $old
     * @return void
     */
    protected function update($new, $old)
    {
        $items = [];

        foreach ($new as $key => $item) {
            if ($this->forceUpdate || $this->needUpdate($new[$key], $old[$key])) {
                if ($this->timestamps) {
                    $item['updated_at'] = $this->time;
                }
                if ($this->delete === 'soft') {
                    $item['deleted_at'] = null;
                }

                $items[] = $item;
            }
        }

        Metis::db($this->connection)->update($this->table, $items, $this->keys, $this->transaction);
    }

    /**
     * Check if a row needs update.
     *
     * @param  array $new
     * @param  array $old
     * @return bool
     */
    protected function needUpdate($new, $old)
    {
        if (isset($old['deleted_at']) && $old['deleted_at']) {
            return true;
        }

        unset($old['created_at'], $old['updated_at']);

        return ! empty(array_diff($new, $old));
    }

    /**
     * Delete data.
     *
     * @param  array $items
     * @return void
     */
    protected function delete($items)
    {
        if ($this->delete === 'soft') {
            Metis::db($this->connection)->softDelete($this->table, $items, $this->keys, $this->time, $this->transaction);
        } else {
            Metis::db($this->connection)->delete($this->table, $items, $this->keys, $this->transaction);
        }
    }
}

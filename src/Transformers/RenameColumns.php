<?php

namespace Marquine\Etl\Transformers;

use Marquine\Etl\Row;

class RenameColumns extends Transformer
{
    /**
     * Transformer columns.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        'columns'
    ];

    /**
     * Transform the given row.
     *
     * @param  \Marquine\Etl\Row  $row
     * @return void
     */
    public function transform(Row $row)
    {
        foreach ($this->columns as $old => $new) {
            $value = $row->get($old);
            $row->remove($old);
            $row->set($new, $value);
        }
    }
}

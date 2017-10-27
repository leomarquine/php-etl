<?php

namespace Marquine\Etl\Utilities;

use Marquine\Etl\Database\Manager as DB;

class SqlStatement extends Utility
{
    /**
     * The connection name.
     *
     * @var string
     */
    public $connection = 'default';

    /**
     * The sql statement.
     *
     * @var array
     */
    public $statement;

    /**
     * Run the utility.
     *
     * @return void
     */
    public function run()
    {
        DB::connection($this->connection)->exec($this->statement);
    }
}

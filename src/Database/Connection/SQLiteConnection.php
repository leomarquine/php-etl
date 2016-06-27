<?php

namespace Marquine\Metis\Database\Connection;

class SQLiteConnection extends Connection
{
    /**
    * Create a new SQLiteConnection instance.
    *
    * @param  array $config
    * @return void
    */
    public function __construct($config)
    {
        $options = $this->getOptions($config);

        $dsn = "sqlite:{$config['database']}";

        $this->connect($dsn, $config, $options);
    }
}

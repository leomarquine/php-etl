<?php

namespace Marquine\Etl\Database\Connectors;

use PDO;

class SqliteConnector extends Connector
{
    /**
    * Connect to a database.
    *
    * @param array $config
    * @return \PDO
    */
    public function connect($config)
    {
        $database = $config['database'];

        return new PDO("sqlite:{$database}", null, null, $this->options);
    }
}

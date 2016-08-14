<?php

namespace Marquine\Metis\Database\Connectors;

use PDO;

class SqliteConnector extends Connector
{
    public function connect($config)
    {
        $database = $config['database'];

        return new PDO("sqlite:{$database}", null, null, $this->options);
        //return new PDO("mysql:host=192.168.20.20;dbname=teste", 'root', '1234', $this->options);
    }
}

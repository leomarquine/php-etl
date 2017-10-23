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
        return $this->createConnection('sqlite:'.$config['database'], $config);
    }
}

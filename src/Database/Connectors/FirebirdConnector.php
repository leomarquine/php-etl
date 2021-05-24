<?php

namespace Marquine\Etl\Database\Connectors;

class FirebirdConnector extends Connector
{
    /**
    * Connect to a database.
    *
    * @param  array  $config
    * @return \PDO
    */
    public function connect($config)
    {
        $dsn = $this->getDsn($config);

        $connection = $this->createConnection($dsn, $config);


        return $connection;

        
    }

       /**
     * Get the DSN string.
     *
     * @param  array  $config
     * @return string
     */
    public function getDsn($config)
    {
        extract($config, EXTR_SKIP);

        $dsn = [];

        if (isset($host) && ! isset($unix_socket)) {
            $dsn['host'] = $host;
        }

        if (isset($port) && ! isset($unix_socket)) {
            $dsn['port'] = $port;
        }
        if (isset($charset) && ! isset($unix_socket)) {
            $dsn['charset'] = $charset;
        }
        if (isset($port) && isset($database) && isset($host) && ! isset($unix_socket)) {
            $dsn['dbname'] = "$host/$port:$database";
        }

        return urldecode('firebird:' . http_build_query($dsn, '', ';'));
        
        
    }
}

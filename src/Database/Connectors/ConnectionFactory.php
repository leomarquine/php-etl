<?php

namespace Marquine\Metis\Database\Connectors;

use InvalidArgumentException;
use Marquine\Metis\Database\SqliteConnection;

class ConnectionFactory
{
    public static function make($config)
    {
        if (! isset($config['driver'])) {
            throw new InvalidArgumentException('A driver must be specified.');
        }

        $driver = $config['driver'];

        $pdo = static::selectConnector($driver)->connect($config);

        return static::selectConnection($driver, $pdo);
    }

    public static function selectConnector($driver)
    {
        switch ($driver) {
            case 'sqlite':
                return new SqliteConnector;

            default:
                # code...
                break;
        }
    }

    public static function selectConnection($driver, $pdo)
    {
        switch ($driver) {
            case 'sqlite':
                return new SqliteConnection($pdo);

            default:
                # code...
                break;
        }
    }
}

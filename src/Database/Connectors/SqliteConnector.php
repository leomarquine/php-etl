<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Database\Connectors;

class SqliteConnector extends Connector
{
    /**
     * Connect to a database.
     */
    public function connect(array $config): \PDO
    {
        return $this->createConnection('sqlite:' . $config['database'], $config);
    }
}

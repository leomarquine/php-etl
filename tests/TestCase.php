<?php

namespace Tests;

use Marquine\Metis\Metis;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $config = [
            'default_path' => __DIR__ . '/data'
        ];

        Metis::config($config);
    }

    protected function setUpDatabase()
    {
        Metis::addConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $this->migrateTables();
    }

    protected function migrateTables($connection = 'default')
    {
        Metis::connection($connection)->exec('DROP TABLE IF EXISTS users');
        Metis::connection($connection)->exec('DROP TABLE IF EXISTS users_ts');

        $schema = new \Doctrine\DBAL\Schema\Schema();

        $users = $schema->createTable('users');
        $users->addColumn('id', 'integer');
        $users->addColumn('name', 'string');
        $users->addColumn('email', 'string');

        $users_ts = $schema->createTable('users_ts');
        $users_ts->addColumn('id', 'integer');
        $users_ts->addColumn('name', 'string');
        $users_ts->addColumn('email', 'string');
        $users_ts->addColumn('created_at', 'datetime', ['notnull' => false]);
        $users_ts->addColumn('updated_at', 'datetime', ['notnull' => false]);
        $users_ts->addColumn('deleted_at', 'datetime', ['notnull' => false]);

        $platform = Metis::connection($connection)->getDatabasePlatform();

        $statements = $schema->toSql($platform);

        foreach ($statements as $statement) {
            Metis::connection($connection)->exec($statement);
        }
    }
}

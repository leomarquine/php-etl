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
        Metis::addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $this->migrateTables();
    }

    protected function migrateTables($connection = 'default')
    {
        Metis::connection($connection)->exec('DROP TABLE IF EXISTS users');
        Metis::connection($connection)->exec('CREATE TABLE users (id INTEGER, name VARCHAR(255), email VARCHAR(255))');
        Metis::connection($connection)->exec('DROP TABLE IF EXISTS users_ts');
        Metis::connection($connection)->exec('CREATE TABLE users_ts (id INTEGER, name VARCHAR(255), email VARCHAR(255), created_at DATETIME, updated_at DATETIME, deleted_at DATETIME)');

        // $schema = new \Doctrine\DBAL\Schema\Schema();
        //
        // $users = $schema->createTable('users');
        // $users->addColumn('id', 'integer');
        // $users->addColumn('name', 'string');
        // $users->addColumn('email', 'string');
        //
        // $users_ts = $schema->createTable('users_ts');
        // $users_ts->addColumn('id', 'integer');
        // $users_ts->addColumn('name', 'string');
        // $users_ts->addColumn('email', 'string');
        // $users_ts->addColumn('created_at', 'datetime', ['notnull' => false]);
        // $users_ts->addColumn('updated_at', 'datetime', ['notnull' => false]);
        // $users_ts->addColumn('deleted_at', 'datetime', ['notnull' => false]);
        //
        // $platform = Metis::connection($connection)->getDatabasePlatform();
        //
        // $statements = $schema->toSql($platform);
        //
        // foreach ($statements as $statement) {
        //     Metis::connection($connection)->exec($statement);
        // }
    }
}

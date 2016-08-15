<?php

namespace Tests;

use Marquine\Etl\Etl;
use PHPUnit_Framework_TestCase;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $config = [
            'default_path' => __DIR__ . '/data',
        ];

        Etl::config($config);
    }

    protected function setUpDatabase()
    {
        Etl::addConnection(['driver' => 'sqlite', 'database' => ':memory:']);

        $this->migrateTables();
    }

    protected function migrateTables($connection = 'default')
    {
        Etl::connection($connection)->exec('DROP TABLE IF EXISTS users');
        Etl::connection($connection)->exec('CREATE TABLE users (id INTEGER, name VARCHAR(255), email VARCHAR(255))');
        Etl::connection($connection)->exec('DROP TABLE IF EXISTS users_ts');
        Etl::connection($connection)->exec('CREATE TABLE users_ts (id INTEGER, name VARCHAR(255), email VARCHAR(255), created_at DATETIME, updated_at DATETIME, deleted_at DATETIME)');
    }
}

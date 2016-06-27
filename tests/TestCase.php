<?php

namespace Tests;

use Marquine\Metis\Metis;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $users = [
        ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
        ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
    ];

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
        Metis::db($connection)->exec('CREATE TABLE users(id INTEGER PRIMARY KEY, name TEXT, email TEXT)');
        Metis::db($connection)->exec('CREATE TABLE users_ts(id INTEGER PRIMARY KEY, name TEXT, email TEXT, created_at TEXT, updated_at TEXT, deleted_at TEXT)');
    }
}

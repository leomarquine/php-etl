<?php

namespace Tests;

use Marquine\Etl\Etl;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $config = [
            'path' => __DIR__ . '/data',
            'database' => [
                'default' => 'primary',
                'connections' => [
                    'primary' => [
                        'driver' => 'sqlite',
                        'database' => ':memory:',
                    ],
                    'secondary' => [
                        'driver' => 'sqlite',
                        'database' => ':memory:',
                    ],
                ],
            ],
        ];

        Etl::config($config);
    }

    protected function createTables($connection = 'default')
    {
        Etl::database($connection)->exec('DROP TABLE IF EXISTS users');
        Etl::database($connection)->exec('CREATE TABLE users (id INTEGER, name VARCHAR(255), email VARCHAR(255))');
        Etl::database($connection)->exec('DROP TABLE IF EXISTS users_ts');
        Etl::database($connection)->exec('CREATE TABLE users_ts (id INTEGER, name VARCHAR(255), email VARCHAR(255), created_at TIMESTAMP, updated_at TIMESTAMP, deleted_at TIMESTAMP)');
    }
}

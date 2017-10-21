<?php

namespace Tests;

use Marquine\Etl\Etl;
use Marquine\Etl\Database\Manager as DB;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp()
    {
        parent::setUp();

        Etl::set('path', __DIR__ . '/data');

        Etl::addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        Etl::addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ], 'secondary');
    }

    protected function createUsersTable($connection, $timestamps = false)
    {
        DB::connection($connection)->exec('DROP TABLE IF EXISTS users');

        $statement = $timestamps
            ? 'CREATE TABLE users (id INTEGER, name VARCHAR(255), email VARCHAR(255), created_at TIMESTAMP, updated_at TIMESTAMP, deleted_at TIMESTAMP)'
            : 'CREATE TABLE users (id INTEGER, name VARCHAR(255), email VARCHAR(255))';

        DB::connection($connection)->exec($statement);
    }
}

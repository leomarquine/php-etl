# Getting Started

## Installation
In your application's folder, run:

```
composer require marquine/php-etl
```

## Database Configuration
If you are working with ETL, you probally need to read and/or write to databases. Currently, we support MySQL, PostgreSQL, SQL Server and SQLite.

These are the configuration parameters for each database driver:

* MySQL
```php
$config = [
    'driver' => 'mysql',
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'database',
    'username' => 'user',
    'password' => 'password',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
];
```

* PostgreSQL
```php
$config = [
    'driver' => 'pgsql',
    'host' => 'localhost',
    'port' => '5432',
    'database' => 'database',
    'username' => 'user',
    'password' => 'password',
    'charset' => 'utf8',
    'schema' => 'public',
];
```

* SQL Server
```php
$config = [
    'driver' => 'sqlsrv',
    'host' => 'localhost',
    'port' => '1433',
    'database' => 'database',
    'username' => 'user',
    'password' => 'password',
];
```

* SQLite
```php
$config = [
    'driver' => 'sqlite',
    'database' => '/path/to/database.sqlite',
];
```
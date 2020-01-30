# Getting Started

## Installation
In your application's folder, run:

```
composer require wizaplace/php-etl
```
## Dependency injection
You can manually instanciate all the classes you need, however it is a good practice to benefit from dependency injection.
The WP-ETL is compatible with any system of a kind.

For instance, in Symfony, you just have to edit your _services.yaml_ file and add this kind
of line for any object you want to be available with DI:

_services.yaml_
```yaml
services:
    Wizaplace\Etl\Etl:
```

Sometimes you may need different instances of the classes. As by default Symfony services container
provide a singleton, but you can ask it explicitly to provide different instances:

_services.yaml_

```yaml
services:
    Wizaplace\Etl\Etl:
      shared: false
```

## Database Configuration
If you are working with ETL, you probably need to read and/or write to databases. Currently, we support MySQL, PostgreSQL, SQL Server and SQLite.

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

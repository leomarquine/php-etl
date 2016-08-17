# PHP ETL
Extract, Transform and Load data using PHP.


## Installation
In your application's folder, run:
```
composer require marquine\php-etl
```


## Setup
### Global configuration
Global configuration can be set using the `config` method. You can skip this configuration and use the full path when working with files.
```php
$config = [
    'default_path' => '/path/to/etl/files',
];

Etl::config($config);
```

### Database
SQLite connection:
```php
$connection = [
    'driver' => 'sqlite',
    'database' => '/path/to/database.sqlite'
];
```

MySQL connection
```php
$connection = [
    'host' => localhost,
    'port' => '3306',
    'database' => dbname,
    'username' => user,
    'password' => pass,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
];
```

PostgreSQL connection
```php
$connection = [
    'driver' => 'pgsql',
    'host' => 'localhost',
    'port' => '5432',
    'database' => 'dbname',
    'username' => 'user',
    'password' => 'pass',
    'charset' => 'utf8',
    'schema' => 'public'
];
```

Adding connections:
```php
use Marquine\Etl\Job;

// default connection
Etl::addConnection($connection);

// named connection
Etl::addConnection($connection, 'connection_name');
```

## Laravel Setup
If you are using Laravel, PHP ETL provides a default configuration file and will register all supported connections of your application.

Add the ServiceProvider to the `providers` array in `config/app.php` file:
```php
Marquine\Etl\Providers\Laravel\EtlServiceProvider::class,
```

Publish the configuration file (`config/etl.php`) using the artisan command:
```
php artisan vendor:publish --provider="Marquine\Etl\Providers\Laravel\EtlServiceProvider"
```

## Example
In the example below, we will extract data from a csv file, trim two columns and load the data into database:
```php
use Marquine\Etl\Job;

Job::start()->extract('csv', 'path/to/file.csv')
    ->transform('trim', ['columns' => ['name', 'email']])
    ->load('table', 'users');
```
or
```php
use Marquine\Etl\Job;

$job = new Job;
$job->extract('csv', 'path/to/file.csv')
    ->transform('trim', ['columns' => ['name', 'email']])
    ->load('table', 'users');
```

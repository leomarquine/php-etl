# PHP ETL
Extract, Transform and Load data using PHP.


## Installation
In your application's folder, run:
```
composer require marquine/php-etl
```


## Setup
Global configuration can be set using the `config` method.
```php
use Marquine\Etl\Etl;

$config = [

    // If not provided, you can use the full path when working with files.
    'path' => '/path/to/etl/files',

    // Currently supported databases: SQLite, MySQL, PostgreSQL
    'database' => [

        'default' => 'sqlite',

        'connections' => [

            'sqlite' => [
                'driver' => 'sqlite',
                'database' => '/path/to/database.sqlite',
            ],

            'mysql' => [
                'driver' => 'mysql',
                'host' => 'localhost',
                'port' => '3306',
                'database' => 'dbname',
                'username' => 'user',
                'password' => 'pass',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
            ],

            'pgsql' => [
                'driver' => 'pgsql',
                'host' => 'localhost',
                'port' => '5432',
                'database' => 'dbname',
                'username' => 'user',
                'password' => 'pass',
                'charset' => 'utf8',
                'schema' => 'public',
            ],

        ],

    ],

];

Etl::config($config);
```


## Laravel Setup
If you are using Laravel, PHP ETL provides a default configuration file and will register all supported connections of your application.

Add the ServiceProvider to the `providers` array in `config/app.php` file:
```php
Marquine\Etl\Providers\Laravel\EtlServiceProvider::class,
```

Publish the configuration file (`config/etl.php`) using the artisan command:
```
php artisan vendor:publish
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

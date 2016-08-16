# PHP ETL
Extract, Transform and Load data using PHP.

## Installation
In your application's folder, run:
```
composer require marquine\php-etl
```

## Documentation
Documentation can be found [here](http://php-etl.readthedocs.io/).


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

## License
PHP ETL is licensed under the [MIT license](http://opensource.org/licenses/MIT).

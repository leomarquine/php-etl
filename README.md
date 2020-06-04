# Wizaplace PHP ETL (WP-ETL)

[![License](https://poser.pugx.org/marquine/php-etl/license)](https://packagist.org/packages/marquine/php-etl)
[![CircleCI](https://circleci.com/gh/wizaplace/php-etl/tree/master.svg?style=svg)](https://circleci.com/gh/wizaplace/php-etl/tree/master)
[![Version](https://img.shields.io/github/v/release/wizaplace/php-etl)](https://circleci.com/gh/wizaplace/php-etl/tree/master)
[![Maintenance](https://img.shields.io/badge/Maintained%3F-yes-green.svg)](https://GitHub.com/wizaplace/php-etl/graphs/commit-activity)
[![Ask Me Anything !](https://img.shields.io/badge/Ask%20me-anything-1abc9c.svg)](https://GitHub.com/wizaplace/php-etl)

Extract, Transform and Load data using PHP.
This library provides classes and a workflow to allow you to extract data from various sources (CSV, DB...), one or many, then transform them before saving them in another format.

You can also easily add your custom classes (Extractors, Transformers and Loaders).

![ETL](docs/img/etl.svg)

## Changelog
See the changelog [here](changelog.MD)

## Installation
In your application's folder, run:
```shell
composer require wizaplace/php-etl
```

## Example :light_rail:
In the example below, we will extract data from a csv file, trim white spaces from the name and email columns and then insert the values into the users table:
```php
use Wizaplace\Etl\Etl;
use Wizaplace\Etl\Extractors\Csv;
use Wizaplace\Etl\Transformers\Trim;
use Wizaplace\Etl\Loaders\Insert;
use Wizaplace\Etl\Database\Manager;
use Wizaplace\Etl\Database\ConnectionFactory;

$connexionFactory = new ConnectionFactory();
$manager = new Manager($connexionFactory);
$etl = new Etl();
$extractor = new Csv();
$transformer = new Trim();
$loader = new Insert($manager);

$etl->extract($extractor, '/path/to/users.csv')
    ->transform($transformer, ['columns' => ['name', 'email']])
    ->load($loader, 'users')
    ->run();
```

The library is fully compatible with any PHP project.
For instance, with Symfony, you can fully benefit from the autowiring. On the following example, you enable it on the
main ETL object, with the _shared_ parameter to _false_ in order to have the possibility to get
different instance of the ETL (optionnal).

_services.yaml_
```yaml
    Wizaplace\Etl\Etl:
        shared: false
```
## Documentation :notebook:
The documentation is available in a subfolder of the repo, [here](docs/README.md).

## License
WP-ETL is licensed under the [MIT license](http://opensource.org/licenses/MIT).

## Origin of the project
This project is a fork and an improvement of the [marquine/php-etl](https://github.com/leomarquine/php-etl) project by [Leonardo Marquine](https://github.com/leomarquine/php-etl).

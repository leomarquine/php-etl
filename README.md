# PHP ETL

[![Build Status](https://travis-ci.org/leomarquine/php-etl.svg)](https://travis-ci.org/leomarquine/php-etl)
[![Latest Stable Version](https://poser.pugx.org/marquine/php-etl/v/stable)](https://packagist.org/packages/marquine/php-etl)
[![Latest Unstable Version](https://poser.pugx.org/marquine/php-etl/v/unstable)](https://packagist.org/packages/marquine/php-etl)
[![License](https://poser.pugx.org/marquine/php-etl/license)](https://packagist.org/packages/marquine/php-etl)

Extract, Transform and Load data using PHP.

## Installation
In your application's folder, run:
```
composer require marquine/php-etl
```

## Documentation
Documentation can be found [here](https://php-etl.gitbook.io/).


## Example
In the example below, we will extract data from a csv file, trim white spaces from the name and email columns and then insert the values into the users table:
```php
use Marquine\Etl\Etl;

$etl = new Etl;

$etl->extract('csv', '/path/to/users.csv')
    ->transform('trim', ['columns' => ['name', 'email']])
    ->load('insert', 'users')
    ->run();
```

## License
PHP ETL is licensed under the [MIT license](http://opensource.org/licenses/MIT).

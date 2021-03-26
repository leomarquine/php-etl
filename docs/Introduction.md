# Introduction

PHP ETL provides the Extract, Transform and Load capabilities that streamline the process of data manipulation.

---

You can, for example, extract data from a csv file, trim white spaces from specific columns and then load the values into a database table:

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
    ->transform($transformer, [$transformer::COLUMNS => ['name', 'email']])
    ->load($loader, 'users')
    ->run();
```

Note that in this above example, we manually instantiate all the object.
However WP-ETL is fully compatible with any DI system, and we highly recommend
to do use DI. See the _Getting started_ section for more details.

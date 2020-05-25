# Running Processes

To run an ETL process, you can chain the steps methods in the desired execution order and then call the `run` method:

```php
$etl->extract(/* ... */)
    ->transform(/* ... */)
    ->load(/* ... */)
    ->run();
```

To return the resulting data as an iterator (ex.: [Chaining ETL's usecase](../tests/UseCases/ChainingTest.php)), you may use the `toIterator` method:

```php
$iterator = $etl->extract(/* ... */)
    ->transform(/* ... */)
    ->toIterator();
```

To run the process and return the resulting data as an array, you may use the `toArray` method:

```php
$data = $etl->extract(/* ... */)
    ->transform(/* ... */)
    ->load(/* ... */)
    ->toArray();
```
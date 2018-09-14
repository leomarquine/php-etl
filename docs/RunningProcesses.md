# Running Processes

To run an ETL process, you can chain the steps methods in the desired execution order and then call the `run` method:

```php
$etl->extract(/* ... */)
    ->transform(/* ... */)
    ->load(/* ... */)
    ->run();
```

To run the process and return the resulting data as an array, you may use the `toArray` method:

```php
$data = $etl->extract(/* ... */)
    ->transform(/* ... */)
    ->load(/* ... */)
    ->toArray();
```

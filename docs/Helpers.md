# Helpers

Besides the three common steps in an ETL process (extract, transform and load), we provide some helpers to assist you with the process data flow.

## Skipping initial lines
To skip a determined number of rows on the beginning of the process, you may use the `skip` method, passing the number of rows to skip as a parameter:

```php
$etl->skip(1);
```

## Limiting the output rows
To limit the maximum number of output rows of the process, you may use the `limit` method, passing the maximum number of rows as a parameter:

```php
$etl->limit(100);
```

## Execution hooks
Sometimes you may need to run tasks right before and/or after a process. You may do so using the `before` and `after` methods:

```php
$etl->before(function () {
    //
});
```

```php
$etl->after(function () {
    //
});
```

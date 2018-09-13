# Query Extractor

Extracts data from a database table using a custom SQL query.

## Options

| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| connection | string | default | Name of the database connection to use. |
| bindings | array | `[]` | Values to bind to the query statement. |


## Usage

Default options:
```php
$etl->extract('query', 'select * from users');
```

Custom options:
```php
$options = [
    'connection' => 'app',
    'bindings' => ['active'],
];

$etl->extract('query', 'select * from users where status = ?', $options);
```

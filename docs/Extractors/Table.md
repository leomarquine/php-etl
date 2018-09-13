# Table Extractor

Extracts data from a database table.

## Options

| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| columns | array | `null` | Columns that will be extracted. |
| connection | string | default | Name of the database connection to use. |
| where | array | `[]` | Array of where clause. |


## Usage

Default options:
```php
$etl->extract('table', 'table_name');
```

Custom options:
```php
$options = [
    'columns' => ['id', 'name'],
    'connection' => 'app',
    'where' => ['status' => 'active'],
];

$etl->extract('table', 'table_name', $options);
```

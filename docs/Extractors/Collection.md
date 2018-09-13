# Collection Extractor

Extracts data from any iterable item. It accepts arrays or traversables objects. The collection items must be associative arrays.

> **Tip:** Using generators will decrease memory usage.

## Options

| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| columns | array | `null` | Columns that will be extracted. |


## Usage

Default options:
```php
$etl->extract('collection', $iterable);
```

Custom options:
```php
$options = [
    'columns' => ['id', 'name']
];

$etl->extract('collection', $iterable, $options);
```

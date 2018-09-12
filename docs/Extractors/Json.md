# JSON Extractor

Extracts data from a JavaScript Object Notation file.

## Options

| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| columns | array | null | Columns that will be extracted. |


## Usage

Default options:
```php
$etl->extract('json', 'path/to/file.json');
```

Custom options:
```php
$options = [
    'columns' => [
        'id' => '$..bindings[*].id.value',
        'name' => '$..bindings[*].name.value'
    ]
];

$etl->extract('json', 'path/to/file.json', $options);
```

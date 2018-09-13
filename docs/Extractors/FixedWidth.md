# Fixed Width Extractor

Extracts data from a text file with fields delimited by a fixed number of characters.

## Options

| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| columns | array | null | Columns that will be extracted. (required) |


## Usage

Custom options:
```php
$options = [
    'columns' => [
        'id' => [0, 5], // Start position (0) and length of column (5).
        'name' => [5, 40], // Start position (5) and length of column (40).
    ]
];

$etl->extract('fixed_width', 'path/to/file.txt', $options);
```

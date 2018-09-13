# CSV Extractor

Extracts data from comma separated files with or without a header row.

## Options

| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| columns | array | null | Columns that will be extracted. |
| delimiter | string | , | Field delimiter (one character only). |
| enclosure | string | | Field enclosure character (one character only). |


## Usage

Default options:
```php
$etl->extract('csv', 'path/to/file.csv');
```

Custom options:
```php
$options = [
    'columns' => [
        'id' => 1, // Index of the column in the csv file (The first column is 1).
        'name' => 2
    ]
];

$etl->extract('csv', 'path/to/file.csv', $options);
```

# XML Extractor

Extracts data from an XML file.

## Options

| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| columns | array | `null` | Columns that will be extracted. |
| loop | string | / | The path to loop. |


## Usage

Default options:
```php
$etl->extract('xml', 'path/to/file.xml');
```

Custom options:
```php
$options = [
    'columns' => [
        'id' => 'id/value',
        'name' => 'name/value',
    ],
    'loop' => '/users/user'
];

$etl->extract('xml', 'path/to/file.xml', $options);
```

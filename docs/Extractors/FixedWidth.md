# Fixed Width

Extracts data from a text file with fields delimited by a fixed number of characters.

```php
/** @var \Wizaplace\Etl\Extractors\FixedWidth $fixedWidth */
$etl->extract($fixedWidth, 'path/to/file.txt', $options);
```

## Options

### Columns (required)

Columns that will be extracted.

| Type  | Default value |
| ----- | ------------- |
| array | `null`        |

Associative array where the `key` is the name of the column and the `value` is an array containing the start position and the length of the column;

```php
$options = [FixedWidth::COLUMNS => [
    'id' => [0, 5], // Start position is 0 and length is 5.
    'name' => [5, 40], // Start position is 5 and length is 40.
]];
```

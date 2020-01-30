# CSV

Extracts data from a character-separated values file.

```php
/** @var \Wizaplace\Etl\Extractors\Csv $csv */
$etl->extract($csv, 'path/to/file.csv', $options);
```


## Options

### Columns
Columns that will be extracted. If `null`, all columns will be extracted and the first line will be used as the columns names.

| Type | Default value |
|----- | ------------- |
| array | `null` |

To select which columns will be extracted, use an array with the columns list:
```php
$options = ['columns' => ['id', 'name', 'email']];
```

To rename the columns, use an associative array where the `key` is the name of the column in the file and the `value` is the name that will be used in the etl process:
```php
$options = ['columns' => [
    'id' => 'id',
    'full_name' => 'name',
    'email_address' => 'email',
]];
```

If your file does not contains the columns names, you may set the name and the index of the columns to extract starting at 1:
```php
$options = ['columns' => [
    'id' => 1,
    'name' => 2,
    'email' => 3,
]];
```

### Delimiter
Field delimiter (one character only).

| Type | Default value |
|----- | ------------- |
| string | , |

```php
$options = ['delimiter' => ';'];
```

### Enclosure
Field enclosure character (one character only).

| Type | Default value |
|----- | ------------- |
| string | |

```php
$options = ['enclosure' => '"'];
```

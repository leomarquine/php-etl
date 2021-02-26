# Column Filter

Filter out columns of `Row` object, by their name
or using a callback function.

```php
/** @var \Wizaplace\Etl\Transformers\ColumnFilterTransformer $transformer */
$etl->transform($transformer, $options);
```

## Options

### Columns

Column names that will be kept in the `Row` object after transformation.
Without additional parameter, any column that doesn't match the name of this parameter will be filtered out.

| Type | Default value |
|----- | ------------- |
| array | `[]` |

```php
$options = ['columns' => ['name', 'email']];
```

### Callback

Callback function to apply on each column, taking the column name and value as parameters, and must return a boolean (true to keep the column, false otherwise).

| Type | Default value |
|----- | ------------- |
| callable | null |

For example, to exclude empty columns:
```php
$options = [
    'callback' => function (string $columnName, $value): bool {
        return !empty($columnName) && !empty($value);
    },
];
```

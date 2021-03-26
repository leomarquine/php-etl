# Rename Columns

Rename columns.

```php
/** @var \Wizaplace\Etl\Transformers\RenameColumns $transformer */
$etl->transform($transformer, $options);
```

## Options

### Columns (required)

Columns that will be transformed. The `key` is the old name and the `value` is the new column name.

| Type  | Default value |
| ----- | ------------- |
| array | `[]`          |

```php
$options = [RenameColumns::COLUMNS => [
    'email_address' => 'email',
]];
```

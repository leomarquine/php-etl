# Rename Columns

Rename columns.

```php
$etl->transform('rename_columns', $options);
```


## Options

### Columns
Columns that will be transformed. The `key` is the old name and the `value` is the new column name.

| Type | Default value |
|----- | ------------- |
| array | `[]` |

```php
$options = ['columns' => [
    'email_address' => 'email',
]];
```

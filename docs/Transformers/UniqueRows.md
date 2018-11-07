# Unique Rows

Remove duplicate rows from the input stream.

```php
$etl->transform('unique_rows', $options);
```


## Options

### Columns
Columns used in the row comparison. If `empty`, all columns will be used.

| Type | Default value |
|----- | ------------- |
| array | `[]` |

```php
$options = ['columns' => ['name', 'email']];
```

### Consecutive
Indicates if only consecutive duplicates will be removed.

| Type | Default value |
|----- | ------------- |
| bool | `false` |

```php
$options = ['consecutive' => true];
```

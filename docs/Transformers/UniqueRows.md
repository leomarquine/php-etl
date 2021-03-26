# Unique Rows

Remove duplicate rows from the input stream.

```php
/** @var \Wizaplace\Etl\Transformers\UniqueRows $transformer */
$etl->transform($transformer, $options);
```

## Options

### Columns

Columns used in the row comparison. If `empty`, all columns will be used.

| Type  | Default value |
| ----- | ------------- |
| array | `[]`          |

```php
$options = [UniqueRows::COLUMNS => ['name', 'email']];
```

### Consecutive

Indicates if only consecutive duplicates will be removed.

| Type | Default value |
| ---- | ------------- |
| bool | `false`       |

```php
$options = [UniqueRows::CONSECUTIVE => true];
```

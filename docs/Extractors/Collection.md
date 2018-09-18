# Collection

Extracts data from any iterable item. It accepts arrays or traversables objects. The collection items must be associative arrays.

```php
$etl->extract('collection', $iterable, $options);
```

> **Tip:** Using generators will decrease memory usage.


## Options

### Delimiter
Columns from the iterable item that will be extracted.

| Type | Default value |
|----- | ------------- |
| array | `null` |

```php
$options = ['columns' => ['id', 'name', 'email']];
```

# Collection

Extracts data from any iterable item. It accepts arrays or traversables objects. The collection items must be associative arrays.

```php
/** @var \Wizaplace\Etl\Extractors\Collection $collection */
$etl->extract($collection, $iterable, $options);
```

> **Tip:** Using generators will decrease memory usage.


## Options

### Columns
Columns from the iterable item that will be extracted.

| Type | Default value |
|----- | ------------- |
| array | `null` |

```php
$options = ['columns' => ['id', 'name', 'email']];
```

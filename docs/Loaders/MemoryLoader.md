# MemoryLoader

Loads data into an in-memory collection.

```php
/** @var \Wizaplace\Etl\Loaders\MemoryLoader $loader */
$etl->load($loader, '', $options);

// Then retrieve data using the index
$loader->get('index_for_row_23');
```

## Options

### Index (required)

The name of `Row` column to uses as in-memory map index.

| Type | Default value |
|----- | ------------- |
| string | `null` |

For example, a row extracted from a CSV with a column named `Identifier` would be loaded with this option set:
```php
$options = ['index' => 'Identifier'];
```

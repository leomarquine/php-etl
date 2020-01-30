# Table

Extracts data from a database table.

```php
/** @var \Wizaplace\Etl\Extractors\Table $table */
$etl->extract($table, 'table_name', $options);
```


## Options

### Columns
Columns that will be extracted. If `null`, all columns of the table will be extracted.

| Type | Default value |
|----- | ------------- |
| array | `null` |

To select which columns will be extracted, use an array with the columns list:
```php
$options = ['columns' => ['id', 'name', 'email']];
```

### Connection
Name of the database connection to use.

| Type | Default value |
|----- | ------------- |
| string | default |

```php
$options = ['connection' => 'app'];
```

### Where
Array of conditions, where `key` equals `value`. If you need more flexibility in the the query creation, you may use the [Query extractor](Query.md).

| Type | Default value |
|----- | ------------- |
| array | `[]` |

```php
$options = ['where' => ['status' => 'active']];
```

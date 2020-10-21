# Insert/Update Loader

Inserts data into a database table.

```php
/** @var \Wizaplace\Etl\Loaders\Insert $insertUpdate */
$etl->load($insertUpdate, 'table_name', $options);
```


## Options

### Columns
Columns that will be loaded. If `null`, all columns in the process will be inserted/updated.

| Type | Default value |
|----- | ------------- |
| array | `null` |

To select which columns will be loaded, use an array with the columns list:
```php
$options = ['columns' => ['id', 'name', 'email']];
```

To map columns from the etl process to the database table, use an associative array where the `key` is the name of the process column and the `value` is the table column:
```php
$options = ['columns' => [
    'id' => 'user_id',
    'name' => 'full_name',
]];
```

### Connection
Name of the database connection to use.

| Type | Default value |
|----- | ------------- |
| string | default |

```php
$options = ['connection' => 'app'];
```

### Key
List of primary keys or identifiers of the table.

| Type | Default value |
|----- | ------------- |
| array | `['id']` |

```php
$options = ['key' => ['id', 'type']];
```

### DoUpdates
When this option is enabled, new rows (based on key) will be inserted, but existing rows will be left unchanged.

The boolean option 'doUpdates' defaults to true, preserving the previous behavior. When set to false, rows that have
keys already present in the destination are skipped rather than updated. This allows new rows to be brought into the
ETL without overwriting any manual edits to the destination table. A future enhancement could be to make it so the
list of columns to update is a subset of the list of columns that are inserted.

| Type | Default value |
|----- | ------------- |
| boolean | `true` |

```php
$options = ['doUpdates' => false];
```

### Timestamps
Populates the `created_at` and/or `updated_at` columns with the current timestamp when inserting or updating a row.

| Type | Default value |
|----- | ------------- |
| boolean | `false` |

```php
$options = ['timestamps' => true];
```

### Transaction
Indicates if the loader will perform database transactions.

| Type | Default value |
|----- | ------------- |
| boolean | `true` |

```php
$options = ['transaction' => false];
```

### Commit Size
Transaction commit size. The transaction option must be enabled.

| Type | Default value |
|----- | ------------- |
| int | 100 |

```php
$options = ['commit_size' => 500];
```

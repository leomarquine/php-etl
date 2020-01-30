# Insert

Inserts data into a database table.

```php
/** @var \Wizaplace\Etl\Loaders\Insert $insert */
$etl->load($insert, 'table_name', $options);
```


## Options

### Columns
Columns that will be loaded. If `null`, all columns in the process will be inserted.

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

### Timestamps
Populates the `created_at` column with the current timestamp when inserting a row.

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

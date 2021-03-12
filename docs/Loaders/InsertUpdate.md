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

If run in a single transaction, treat the ETL process as a single atomic transaction and roll back on errors. If
run in multiple transactions, the best we can do is provide durability by trying to commit any inserts that are
accepted by the destination database.

| Type | Default value |
|----- | ------------- |
| boolean | `true` |

```php
$options = ['transaction' => false];
```

### Commit Size
Transaction commit size. The transaction option must be enabled.

The work in done in a single transaction if commit size is zero, and we want to roll back that transaction if an
insert fails. In that manner, the ETL process becomes ACID in that either all the inserts are committed or none
are. If the ETL process fails, we can replay the entire source after fixing the error.

If the work is done in multiple transactions, however, some transactions may have already been committed. The
inserts from later pending transactions therefore are not atomic or durable in the sense that the pipeline
can fail and inserts that are accepted still need to be committed. This would leave the database in a state
where it is difficult to determine which inserts have been accepted and which have not. Therefore, we try to
commit the pending transaction so any rows that have been reported as inserted will be durable in the database.
In terms of ACID properties of the destination database, since committing multiple transactions implies the
ETL process is not atomic, at least we can be durable.

| Type | Default value |
|----- | ------------- |
| int | 100 |

```php
$options = ['commit_size' => 500];
```

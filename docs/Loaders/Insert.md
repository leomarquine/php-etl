# Insert Loader

Inserts data into a database table.

## Options

| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| columns | array | `null` | Columns that will be loaded. |
| connection | string | default | Name of the database connection. |
| timestamps | boolean | `false` | Use `created_at` column when inserting a row. |
| transaction | boolean | `true` | Indicates if the loader will perform database transactions. |
| commit_size | int | 100 | The transaction commit size. |


## Usage

```php
$etl->load('insert', 'table_name', $options);
```

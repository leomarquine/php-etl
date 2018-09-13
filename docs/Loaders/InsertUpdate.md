# Insert/Update Loader

Inserts data into a database table.

## Options

| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| columns | array | `null` | Columns that will be loaded. |
| connection | string | default | Name of the database connection. |
| key | array | `['id']` | List of primary keys or identifiers of the table. |
| timestamps | boolean | `false` | Use `created_at` and `updated_at` columns when inserting or updating a row. |
| transaction | boolean | `true` | Indicates if the loader will perform database transactions. |
| commit_size | int | 100 | The transaction commit size. |


## Usage

```php
$etl->load('insert_update', 'table_name', $options);
```

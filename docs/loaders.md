# Loaders
```php
$job->load($type, $destination, $options);
```
* `$type`: the type of the loader (table, etc).
* `$destination`: the destination of the data (table_name, etc).
* `$options`: an array containing the loader options.


## Table
### Syntax
```php
$job->load('table', $destination, $options);
```
### Options
| Name | Type   | Default | Description |
| ---- | ------ |-------- | ----------- |
| connection | string | 'default' | Name of the database connection.  |
| keys | array&#124;string | ['id'] | List of primary keys or identifiers of the table. |
| insert | boolean | true | Insert rows that are in the source but not in the destination table. |
| update | boolean | true | Update rows (based on primary_key option) that are in both source and destination and have new values from the source. |
| delete | boolean&#124;string | false | Delete rows that are in destination table but not in the source. If set to `soft`, the row will not be deleted and the column deleted_at will be set to the current timestamp. |
| skipDataCheck | boolean | false | Do not check table current data before `insert`, `update` and `delete` statements execution. |
| forceUpdate | boolean | false | Do not check for differences between source and destination when updating. |
| timestamps | boolean | false | Use `created_at` and `updated_at` columns when inserting or updating. |
| transaction | boolean&#124;int | 100 | Transaction size. Set to `false` to execute statements without transactions. |

### Examples
Load data to a database table:
```php
$job->load('table', 'table_name');
```
Load data to a database table using timestamps and custom primary key:
```php
$options = [
    'timestamps' => true,
    'keys' => ['id', 'company_id']
];

$job->load('table', 'table_name', $options);
```

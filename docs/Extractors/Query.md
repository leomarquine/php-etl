# Query

Extracts data from a database table using a custom SQL query.

```php
/** @var \Wizaplace\Etl\Extractors\Query $query */
$etl->extract($query, 'select * from users', $options);
```

## Options

### Connection

Name of the database connection to use.

| Type   | Default value |
| ------ | ------------- |
| string | default       |

```php
$options = [Query::CONNECTION => 'app'];
```

### Bindings

Values to bind to the query statement.

| Type  | Default value |
| ----- | ------------- |
| array | `[]`          |

Using prepared statement with named placeholders `select * from users where status = :status`:

```php
$options = [Query::BINDINGS => ['status' => 'active']];
```

Using prepared statement with question mark placeholders `select * from users where status = ?`:

```php
$options = [Query::BINDINGS => ['active']];
```

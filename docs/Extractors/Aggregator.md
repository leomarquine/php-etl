# Aggregator

Merge rows from a list of partial data iterators with a matching index.

```php
use \Wizaplace\Etl\Etl;

# user data from one CSV file
$userDataIterator = (new Etl())
    ->extract(
        new Csv(),
        'user_data.csv',
        ['columns' => ['id','email', 'name']]
    )
    ->toIterator()
;

# extended info from another source
$extendedInfoIterator = (new Etl())
    ->extract(
        new Table(),
        'extended_info',
        ['columns' => 'courriel', 'twitter']
    )
    # let's rename 'courriel' to 'email'
    ->transform(
        new RenameColumns(),
        [
            'columns' => ['courriel' => 'email']
        ]
    )
    ->toIterator()
;

# merge this two data sources
$mergedData = (new Etl())
    ->extract(
        new Aggregator(),
        [
            $userDataIterator,
            $extendedInfoIterator,
        ],
        [
            'index' => ['email'], # common matching index
            'columns' => ['id','email','name','twitter']
        ]
    )
    ->load(
        new CsvLoader(),
        'completeUserData.csv'
    )
    ->run()
;
```

## Options

### Index (required)

An array of column names common in all data sources. Note: be careful when using numerical values, they must be of the same type.

| Type  | Default value |
|-------|---------------|
| array | `null`        |

```php
$options = ['index' => ['email']];
```

### Columns (required)

A `Row` is yield when all specified columns have been found for the matching index.

| Type  | Default value |
|-------|---------------|
| array | `null`        |

```php
$options = ['columns' => ['id', 'name', 'email']];
```

### Strict

When all Iterators input are fully consummed, if we have any remaining incomplete rows:

- if *true*: Throw an `IncompleteDataException`
- if *false*: yield the incomplete remaining `Row` flagged as `incomplete`

| Type    | Default value |
|---------|---------------|
| boolean | `true`        |

```php
$options = ['strict' => false];
```

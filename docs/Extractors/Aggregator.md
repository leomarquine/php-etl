# Aggregator

Merge rows from a list of partial data iterators with a matching index.

```php
use \Wizaplace\Etl\Etl;

# user data from one CSV file
$userDataIterator = (new Etl())
    ->extract(
        new Csv(),
        'user_data.csv',
        [Csv::COLUMNS => ['id','email', 'name']]
    )
    ->toIterator()
;

# extended info from another source
$extendedInfoIterator = (new Etl())
    ->extract(
        new Table(),
        'extended_info',
        [Table::COLUMNS => 'courriel', 'twitter']
    )
    # let's rename 'courriel' to 'email'
    ->transform(
        new RenameColumns(),
        [
            RenameColumns::COLUMNS => ['courriel' => 'email']
        ]
    )
    ->toIterator()
;

# merge these two data sources, capture result in "completeUserData.csv".
$pipeline = new Etl();
$pipeline
    ->extract(
        new Aggregator(),
        [
            $userDataIterator,
            $extendedInfoIterator,
        ],
        [
            Aggregator::INDEX => ['email'], # common matching index
            Aggregator::COLUMNS => ['id','email','name','twitter']
        ]
    )
    ->load(
        new CsvLoader(),
        'completeUserData.csv'
    )
    ->run();
```

## Options

### Index (required)

An array of column names common in all data sources. Note: be careful when using numerical values, they must be of the same type.

| Type  | Default value |
| ----- | ------------- |
| array | `null`        |

```php
$options = [Aggregator::INDEX => ['email']];
```

### Columns (required)

A `Row` is yield when all specified columns have been found for the matching index.

| Type  | Default value |
| ----- | ------------- |
| array | `null`        |

```php
$options = [
    Aggregator::COLUMNS => [
        'id',
        'name',
        'email'
    ]
];
```

### Strict

When all Iterators input are fully consumed, if we have any remaining incomplete rows, an `IncompleteDataException` is thrown if `strict` is `true`

| Type    | Default value |
| ------- | ------------- |
| boolean | `true`        |

```php
$options = [Aggregator::STRICT => false];
```

### Discard

If `strict` is `false` and `discard` is `true` we yield the incomplete remaining `Rows` flagged as `incomplete`

| Type    | Default value |
| ------- | ------------- |
| boolean | `false`       |

```php
$options = [Aggregator::DISCARD' => false];
```

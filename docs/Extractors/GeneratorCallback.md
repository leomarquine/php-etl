# GeneratorCallback

Uses a Generator callback to yield multiple rows for each row of an input.

In particular, can use $pipeline->extract(...)->toIterator() as input to this Extractor.
In such a configuration, two extractors can be chained together to return N x M rows
selecting N rows of JSON arrays from an SQL database, each containing M elements in the
JSON array.

```php
$iterable = [
    ['id' => 1, 'json' => '["a", "b", "c"]'],
    ['id' => 2, 'json' => '["x", "y", "z"]'],
];

$options[GeneratorCallback::CALLBACK] = function ($row) {
    foreach (json_decode($row['json']) as $value) {
        yield ['id' => $row['id'], 'value' => $value];
    }
};

/** @var \Wizaplace\Etl\Etl $pipeline */
/** @var \Wizaplace\Etl\Extractors\GeneratorCallback $extractor */
$pipeline->extract($extractor, $iterable, $options);

// Alternatively...
/** @var \Wizaplace\Etl\Etl $source */
/** @var \Wizaplace\Etl\Extractors\Table $tableExtractor */
$iterable = $source->extract($tableExtractor, 'tableName', [Table::CONNECTION => 'default'])->toArray();
$pipeline->extract($extractor, $iterable, $options);
```

> **Tip:** Using generators will decrease memory usage.

## Options

### Callback (required)

A callback function that takes a row array as its input and returns an iterable object.

| Type     | Default value |
| -------- | ------------- |
| callable | `null`        |

```php
$callback = function ($row) {
    foreach (json_decode($row['json']) as $value) {
        yield ['id' => $row['id'], 'value' => $value];
    }
};
$options = [GeneratorCallback::CALLBACK => $callback];
```

The value of Option::CALLBACK can also be an array of `['\Fully\Qualified\ClassName', 'aStaticFunction']`.

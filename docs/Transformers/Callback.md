# Transform Columns via Callback

Transform Columns via Row Callback.

```php
/**
 * @var \Wizaplace\Etl\Etl $pipeline
 * @var \Wizaplace\Etl\Transformers\Callback $transformer
 * @var array $options
 */
$pipeline->transform($transformer, $options);
```


## Options

### Columns (required)
Columns that will be transformed. The `key` is the old name and the `value` is the new column name.

| Type | Default value |
|----- | ------------- |
| array | `[]` |

```php
$options = ['columns' => ['name']];
```

### Callback (required)
The callback function, which takes two arguments: the Row object, and the column to be transformed

| Type     | Default value |
|--------- | ------------- |
| callable | `null`        |

```php
use Wizaplace\Etl\Row;
$options = ['callback' => function(Row $row, $columnName) {
    return "{$row->get($columnName)} <{$row->get('someOtherColumn')}>";
}];
```

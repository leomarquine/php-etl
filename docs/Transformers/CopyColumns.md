# Copy Columns

Copy columns.

```php
/**
 * @var \Wizaplace\Etl\Etl $pipeline
 * @var \Wizaplace\Etl\Transformers\RenameColumns $transformer
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
$options = ['columns' => [
    'email_address' => 'email',
]];
```

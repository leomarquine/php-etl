# JSON Encode

Converts a value into its JSON representation.

```php
/** @var \Wizaplace\Etl\Transformers\JsonEncode $transformer */
$etl->transform($transformer, $options);
```


## Options

### Columns
Columns that will be transformed. If `empty`, the transformation is applied to all columns.

| Type | Default value |
|----- | ------------- |
| array | `[]` |

```php
$options = ['columns' => ['preferences']];
```

### Options
Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, JSON_PRESERVE_ZERO_FRACTION, JSON_UNESCAPED_UNICODE, JSON_PARTIAL_OUTPUT_ON_ERROR. The behaviour of these constants is described on the [JSON constants](http://php.net/manual/en/json.constants.php) page.

| Type | Default value |
|----- | ------------- |
| int | 0 |

```php
$options = ['options' => JSON_FORCE_OBJECT];
```

### Depth
The maximum depth. Must be greater than zero.

| Type | Default value |
|----- | ------------- |
| int | 512 |

```php
$options = ['depth' => 32];
```

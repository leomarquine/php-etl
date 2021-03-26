# JSON Decode

Decodes a JSON string.

```php
/** @var \Wizaplace\Etl\Transformers\JsonDecode $transformer */
$etl->transform($transformer, $options);
```

## Options

### Columns

Columns that will be transformed. If `empty`, the transformation is applied to all columns.

| Type  | Default value |
| ----- | ------------- |
| array | `[]`          |

```php
$options = [JsonDecode::COLUMNS => ['preferences']];
```

### Associative

Indicates if objects will be converted to associative arrays.

| Type | Default value |
| ---- | ------------- |
| bool | `false`       |

```php
$options = [JsonDecode::ASSOC => true];
```

### Depth

The maximum depth. Must be greater than zero.

| Type | Default value |
| ---- | ------------- |
| int  | 512           |

```php
$options = [JsonDecode::DEPTH => 32];
```

### Options

Bitmask of JSON decode options. Currently there are two supported options. The first is JSON_BIGINT_AS_STRING that allows casting big integers to string instead of floats which is the default. The second option is JSON_OBJECT_AS_ARRAY that has the same effect as setting assoc to TRUE.

| Type | Default value |
| ---- | ------------- |
| int  | 0             |

```php
$options = [JsonDecode::OPTIONS => JSON_FORCE_OBJECT];
```

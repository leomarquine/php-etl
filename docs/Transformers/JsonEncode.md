# JSON Encode Transformer

Converts a value into its JSON representation.

## Options

| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| columns | array | `null` | Columns that will be transformed. |
| options | int | 0 | Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, JSON_PRESERVE_ZERO_FRACTION, JSON_UNESCAPED_UNICODE, JSON_PARTIAL_OUTPUT_ON_ERROR. The behaviour of these constants is described on the [JSON constants](http://php.net/manual/en/json.constants.php) page. |
| depth | int | 512 | The maximum depth. Must be greater than zero. |


## Usage

Applying default options to all columns:
```php
$etl->transform('json_encode');
```

Custom options:
```php
$etl->transform('json_encode', [ /* transformer options */ ]);
```

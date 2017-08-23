# Transformers
```php
$job->transform($type, $options);
```
* `$type`: the type of the transformer (trim, etc).
* `$options`: an array containing the transformer options.


## Trim
### Syntax
```php
$job->transform('trim', $options);
```
### Options
| Name | Type   | Default | Description |
| ---- | ------ |-------- | ----------- |
| type | string | 'both' | The options `trim` &#124; `all` &#124; `both` will trim both sides, `ltrim` &#124; `start` &#124; `left` will trim the left side and `rtrim` &#124; `end` &#124; `right` will trim the right side of the string.  |
| mask | string | "&nbsp;\t\n\r\0\x0B" | The stripped characters. Simply list all characters that you want to be stripped. With .. you can specify a range of characters. |

### Examples
Strip whitespace from the beginning and end of a string in all transformation columns:
```php
$job->transform('trim');
```
Strip pipes from the beginning of a string in specific transformation columns:
```php
$options = [
    'columns' => ['id', 'name'],
    'type' => 'left',
    'mask' => '|'
];

$job->transform('trim', $options);
```

## Json Encode
### Syntax
```php
$job->transform('JsonEncode', $options);
```
### Options
| Name | Type   | Default | Description |
| ---- | ------ |-------- | ----------- |
| columns | array | null | Columns that will be transformed. |
| options | int | 0 | Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, JSON_PRESERVE_ZERO_FRACTION, JSON_UNESCAPED_UNICODE, JSON_PARTIAL_OUTPUT_ON_ERROR. The behaviour of these constants is described on the [JSON constants](http://php.net/manual/en/json.constants.php) page.  |
| depth | int | 512 | The maximum depth. Must be greater than zero. |

### Examples
Convert all columns to JSON:
```php
$job->transform('JsonEncode');
```
Convert specific columns to JSON:
```php
$options = [
    'columns' => ['data'],
];

$job->transform('JsonEncode', $options);
```

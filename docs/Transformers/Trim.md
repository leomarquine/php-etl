# JSON Encode Transformer

Strip whitespace (or other characters) from the beginning and/or end of a string.

## Options

| Name | Type | Default | Description |
| ---- |----- | ------- | ----------- |
| type | string | both | The options `trim` &#124; `all` &#124; `both` will trim both sides, `ltrim` &#124; `start` &#124; `left` will trim the left side and `rtrim` &#124; `end` &#124; `right` will trim the right side of the string.  |
| mask | string | "&nbsp;\t\n\r\0\x0B" | The stripped characters. Simply list all characters that you want to be stripped. With .. you can specify a range of characters. |


## Usage

Applying default options to all columns:
```php
$etl->transform('trim');
```

Custom options:
```php
$etl->transform('trim', [ /* transformer options */ ]);
```

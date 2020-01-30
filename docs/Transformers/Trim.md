# Trim

Strip whitespace (or other characters) from the beginning and/or end of a string.

```php
/** @var \Wizaplace\Etl\Transformers\Trim $transformer */
$etl->transform($transformer, $options);
```


## Options

### Columns
Columns that will be transformed. If `empty`, the transformation is applied to all columns.

| Type | Default value |
|----- | ------------- |
| array | `[]` |

```php
$options = ['columns' => ['name', 'email']];
```

### Type
The options `trim` &#124; `all` &#124; `both` will trim both sides, `ltrim` &#124; `start` &#124; `left` will trim the left side and `rtrim` &#124; `end` &#124; `right` will trim the right side of the string.

| Type | Default value |
|----- | ------------- |
| string | both |

```php
$options = ['type' => 'right'];
```

### Mask
The stripped characters. Simply list all characters that you want to be stripped. With .. you can specify a range of characters.

| Type | Default value |
|----- | ------------- |
| string | "&nbsp;\t\n\r\0\x0B" |

```php
$options = ['mask' => '|'];
```

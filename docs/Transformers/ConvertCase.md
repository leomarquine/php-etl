# Convert Case

Convert string case.

```php
/** @var \Wizaplace\Etl\Transformers\ConvertCase $transformer */
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

### Mode
The mode of the conversion. It can be `lower`/`lowercase`, `upper`/`uppercase` or `title`

| Type | Default value |
|----- | ------------- |
| string | lower |

```php
$options = ['mode' => 'upper'];
```

### Encoding
The character encoding.

| Type | Default value |
|----- | ------------- |
| string | utf-8 |

```php
$options = ['encoding' => 'ASCII'];
```

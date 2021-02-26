# Format Unix Time

Formats a Unix time (seconds since 1970-01-01).

```php
/**
 * @var \Wizaplace\Etl\Etl $pipeline
 * @var \Wizaplace\Etl\Transformers\FormatUnixTime $transformer
 * @var array $options
 */
$pipeline->transform($transformer, $options);
```


## Options

### Columns (required)
Columns that will be transformed.

| Type | Default value |
|----- | ------------- |
| array | `[]`         |

```php
$options = ['columns' => ['timestamp']];
```

### Format
The desired output format, as used in DateTime::format().

| Type   | Default value |
|------- | ------------- |
| string | `Ymd`         |

```php
$options = ['format' => 'Y-m-d H:i:s'];
```

### Timezone
The timezone for which the time should be rendered, uses PHP setting if not specified.

| Type   | Default value |
|------- | ------------- |
| string | `null`        |

```php
$options = ['timezone' => 'UTC'];
```

# XML Extractor

Extracts data from an XML file.

```php
/** @var \Wizaplace\Etl\Extractors\Xml $xml */
$etl->extract($xml, 'path/to/file.xml', $options);
```


## Options

### Columns
Columns that will be extracted. If `null`, all tags and attributes within the loop path will be extracted.

| Type | Default value |
|----- | ------------- |
| array | `null` |

To select which columns will be extracted, use the path (without the loop path) of the value. Use `@` to select attributes:
```php
$options = ['columns' => [
    'id' => '/@id',
    'name' => '/profile/name',
    'email' => '/profile/email',
]];
```

### Loop
The path to loop through.

| Type | Default value |
|----- | ------------- |
| string | / |

To select which columns will be extracted, use the path (without the loop path) of the value. Use `@` to select attributes:
```php
$options = ['loop' => '/users/user'];
```

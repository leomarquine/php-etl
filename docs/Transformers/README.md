# Transformers

Manipulates data, in sequence, from the data source previously defined in the extractor. They can be used several times in the same task before and after loaders. Transformers receive two arguments: type and options (optional).

```php
$etl->transform('type', $options);
```

## Available transformers types

* [JSON Encode](JsonEncode.md)

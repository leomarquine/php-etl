# Transformers

Manipulates data, in sequence, from the data source previously defined in the extractor. They can be used several times in the same task before and after loaders. Transformers receive two arguments: type and configuration (optional).

```php
$etl->transform('type', $config);
```

## Available transformers types

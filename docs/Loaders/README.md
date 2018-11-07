# Loaders

Loaders are the data outputs of etl. They can be used multiple times in the same process. Loaders receive three arguments: type, destination and options (optional).

```php
$etl->load('type', $destination, $options);
```


## Available loaders types

* [Insert](Insert.md)
* [Insert/Update](InsertUpdate.md)

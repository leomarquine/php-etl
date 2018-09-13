# Extractors

Extractors are the starting point of any task. To start a task, you must set up an extractor to read a data source such as a csv file or a database table. Extractors receive three arguments: type, source and configuration (optional).

```php
$etl->extract('type', $source, $config);
```

## Available extractors types

* [Collection](Collection.md)
* [CSV](Csv.md)
* [Fixed Width](FixedWidth.md)
* [JSON](Json.md)
* [Query](Query.md)
* [Table](Table.md)
* [XML](Xml.md)

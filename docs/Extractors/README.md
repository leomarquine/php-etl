# Extractors

Extractors are the entry point of any process. To start a process, you must set up an extractor to read a data source such as a csv file or a database table. Extractors receive three arguments: type, source and options (optional).

```php
/** @var \Wizaplace\Etl\Extractors\Extractor $type */
$etl->extract($type, $source, $options);
```

## Available extractors types

* [Aggregator](Aggregator.md)
* [Collection](Collection.md)
* [CSV](Csv.md)
* [Fixed Width](FixedWidth.md)
* [JSON](Json.md)
* [Query](Query.md)
* [Table](Table.md)
* [XML](Xml.md)

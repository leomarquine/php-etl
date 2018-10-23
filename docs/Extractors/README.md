# Extractors

Extractors are the entry point of any process. To start a process, you must set up an extractor to read a data source such as a csv file or a database table. Extractors receive three arguments: type, source and options (optional).

```php
$etl->extract('type', $source, $options);
```


## Available extractors types

* [Collection](Collection.md)
* [CSV](Csv.md)
* [Fixed Width](FixedWidth.md)
* [JSON](Json.md)
* [Query](Query.md)
* [Table](Table.md)
* [XML](Xml.md)


## Custom extractor

To make a custom extractor, you need to create a class that extends the base extractor `Marquine\Etl\Extractors\Extractor`.

The `extract` method will receive the data source as a parameter. In this method, you must perform all the necessary tasks to prepare the extractor iteration to run.

The `getIterator` method must return a `Traversable` object. Each iteration item (row) must be an associative array, where `key` is the column name and `value` is the column value.

Properties that can be configured as options must be in the `availableOptions` array.

```php
use Marquine\Etl\Extractors\Extractor;

class CustomExtractor extends Extractor
{
    /**
     * Properties that can be set via the options method.
     *
     * @var array
     */
    protected $availableOptions = [
        //
    ];

    /**
     * Set up the extraction from the given source.
     *
     * @param  mixed  $source
     * @return void
     */
    public function extract($source)
    {
        //
    }

    /**
     * Get the extractor iterator.
     *
     * @return \Generator
     */
    public function getIterator()
    {
        //
    }
}
```

### Using the extractor
You can make a new instance of the extractor or provide its class string and we will try to resolve any possible dependency:
```php
$etl->extract(new CustomExtractor, 'source', $options);
$etl->extract(CustomExtractor::class, 'source', $options);
```

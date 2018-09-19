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

If you want to make your own custom extractor, you need to create a class that implements the `ExtractorInterface`.
```php
use Marquine\Etl\Extractors\ExtractorInterface;

class CustomExtractor implements ExtractorInterface
{
    /**
     * Set the extractor source.
     *
     * @param  mixed  $source
     * @return void
     */
    public function source($source)
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

### Setting the source
The `source` method will register the source of your custom extractor. It will receive the source from the `extract` step in the etl.

```php
/**
 * Set the extractor source.
 *
 * @param  mixed  $source
 * @return void
 */
public function source($source)
{
    $this->source = $source;
}
```

### Data generator
The `getIterator` method must return a `Generator`. Each item of the iteration must be an associative array.

```php
/**
 * Get the extractor iterator.
 *
 * @return \Generator
 */
public function getIterator()
{
    yield ['id' => '1', 'key', 'value'];
    yield ['id' => '2', 'key', 'value'];
}
```

### Setting options
Any pulic property can be set by the options argument of the step. For example, to set the `columns` public property of the extractor to `['name', 'email']`:
```php
$options = ['columns' => ['name', 'email']];
```

### Using the extractor
You can create a new instance of the extractor or provide its class string and we will try to resolve any possible dependency:
```php
$etl->extract(new CustomExtractor, 'source', $options);
$etl->extract(CustomExtractor::class, 'source', $options);
```

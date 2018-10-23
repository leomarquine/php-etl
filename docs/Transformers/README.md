# Transformers

Manipulates data, in sequence, from the data source previously defined in the extractor. They can be used multiple times in the same process before and after loaders. Transformers receive two arguments: type and options (optional).

```php
$etl->transform('type', $options);
```


## Available transformers types

* [Convert Case](ConvertCase.md)
* [JSON Decode](JsonDecode.md)
* [JSON Encode](JsonEncode.md)
* [Rename Columns](RenameColumns.md)
* [Trim](Trim.md)


## Custom transformer

To make a custom transformer, you need to create a class that extends the base transformer `Marquine\Etl\Transformers\Transformer`.

The `transform` method must return a callback that will handle the transformation when the ETL process run. For each iteration, the callback will receive the current row to perform all the necessary transformations and then return the row.

Properties that can be configured as options must be in the `availableOptions` array.

```php
use Marquine\Etl\Transformers\Transformer;

class CustomTransformer extends Transformer
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
     * Get the transformer handler.
     *
     * @return callable
     */
    public function transform()
    {
        //
    }
}
```

### Using the transformer
You can make a new instance of the transformer or provide its class string and we will try to resolve any possible dependency:
```php
$etl->transform(new CustomTransformer, $options);
$etl->transform(CustomTransformer::class, $options);
```

# Transformers

Manipulates data, in sequence, from the data source previously defined in the extractor. They can be used multiple times in the same process before and after loaders. Transformers receive two arguments: type and options (optional).

```php
$etl->transform('type', $options);
```

## Available transformers types

* [Convert Case](ConvertCase.md)
* [JSON Decode](JsonDecode.md)
* [JSON Encode](JsonEncode.md)
* [Trim](Trim.md)


## Custom transformer

If you want to make your own custom transformer, you need to create a class that implements the `TransformerInterface`.
```php
use Marquine\Etl\Transformers\TransformerInterface;

class CustomTransformer implements TransformerInterface
{
    /**
     * Get the transformer handler.
     *
     * @param  \Marquine\Etl\Pipeline  $pipeline
     * @return callable
     */
    public function handler(Pipeline $pipeline);
}
```

### Transformer handler
The `handler` method will receive the `Pipeline` instance. The method must return a callback that will receive a `$row` and a `$metadata` variable. This callback will execute the transformation for each row at a time.
```php
/**
 * Get the transformer handler.
 *
 * @param  \Marquine\Etl\Pipeline  $pipeline
 * @return callable
 */
public function handler(Pipeline $pipeline)
{
    // ...

    return function ($row, $metada) {
        // transform the row
    };
}
```

### Setting options
Any pulic property can be set by the options argument of the step. For example, to set the `columns` public property of the transformer to `['name', 'email']`:
```php
$options = ['columns' => ['name', 'email']];
```

### Using the transformer
You can create a new instance of the transformer or provide its class string and we will try to resolve any possible dependency:
```php
$etl->transform(new CustomTransformer, $options);
$etl->transform(CustomTransformer::class, $options);
```

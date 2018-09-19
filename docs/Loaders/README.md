# Loaders

Loaders are the data outputs of etl. They can be used multiple times in the same process. Loaders receive three arguments: type, destination and options (optional).

```php
$etl->load('type', $destination, $options);
```

## Available loaders types

* [Insert](Insert.md)
* [Insert/Update](InsertUpdate.md)


## Custom loader

If you want to make your own custom loader, you need to create a class that implements the `LoaderInterface`.
```php
use Marquine\Etl\Loaders\LoaderInterface;

class CustomLoader implements LoaderInterface
{
    /**
     * Get the loader handler.
     *
     * @param  \Marquine\Etl\Pipeline  $pipeline
     * @param  string  $destination
     * @return callable
     */
    public function handler(Pipeline $pipeline, $destination);
}
```

### Loader handler
The `handler` method will receive the `Pipeline` instance and the `destination`. The method must return a callback that will receive a `$row` and a `$metadata` variable. This callback will execute the load process for each row at a time.
```php
/**
 * Get the loader handler.
 *
 * @param  \Marquine\Etl\Pipeline  $pipeline
 * @param  string  $destination
 * @return callable
 */
public function handler(Pipeline $pipeline, $destination)
{
    // ...

    return function ($row, $metada) {
        // load data to destination
    };
}
```

### Setting options
Any pulic property can be set by the options argument of the step. For example, to set the `columns` public property of the loader to `['name', 'email']`:
```php
$options = ['columns' => ['name', 'email']];
```

### Using the loader
You can create a new instance of the loader or provide its class string and we will try to resolve any possible dependency:
```php
$etl->load(new CustomLoader, 'destination', $options);
$etl->load(CustomLoader::class, 'destination', $options);
```

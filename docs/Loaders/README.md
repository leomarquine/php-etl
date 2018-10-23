# Loaders

Loaders are the data outputs of etl. They can be used multiple times in the same process. Loaders receive three arguments: type, destination and options (optional).

```php
$etl->load('type', $destination, $options);
```


## Available loaders types

* [Insert](Insert.md)
* [Insert/Update](InsertUpdate.md)


## Custom loader

To make a custom loader, you need to create a class that extends the base loader `Marquine\Etl\Loaders\Loader`.

The `load` method must return a callback that will handle the data load when the ETL process run. For each iteration, the callback will receive the current row to perform the load process and then return the row.

Properties that can be configured as options must be in the `availableOptions` array.

```php
use Marquine\Etl\Loaders\Loader;

class CustomLoader extends Loader
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
     * Get the loader handler.
     *
     * @param  mixed  $destination
     * @return callable
     */
    public function load($destination)
    {
        //
    }
}
```

### Using the loader
You can make a new instance of the loader or provide its class string and we will try to resolve any possible dependency:
```php
$etl->transform(new CustomLoader, $options);
$etl->transform(CustomLoader::class, $options);
```

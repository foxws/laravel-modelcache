# Laravel model cache helper

[![Latest Version on Packagist](https://img.shields.io/packagist/v/foxws/laravel-modelcache.svg?style=flat-square)](https://packagist.org/packages/foxws/laravel-modelcache)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/foxws/laravel-modelcache/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/foxws/laravel-modelcache/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/foxws/laravel-modelcache/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/foxws/laravel-modelcache/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/foxws/laravel-modelcache.svg?style=flat-square)](https://packagist.org/packages/foxws/laravel-modelcache)

This package allows the Laravel Cache driver to be easily used for model instances. By default, logged in users will have their own separate cache-prefix.

## Installation

Install the package via composer:

```bash
composer require foxws/laravel-modelcache
```

Publish the config file with:

```bash
php artisan vendor:publish --tag="modelcache-config"
```

## Usage

### Model Concern

Implement the `Foxws\ModelCache\Concerns\InteractsWithModelCache` trait to your Eloquent model:

```php
use Foxws\ModelCache\Concerns\InteractsWithModelCache;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use InteractsWithModelCache;
}
```

### Facade

It is also possible to use the `ModelCache` Facade directly:

```php
use Foxws\ModelCache\Facades\ModelCache;

class MyActionClass
{
    public function handle(Video $model): void
    {
        if (! ModelCache::enabled()) {
            // modelcaching is disabled
            return;
        }

        ModelCache::cache($model, 'foo', 'bar');
        ModelCache::hasBeenCached($model, 'foo');
        ModelCache::getCachedValue($model, 'foo');
    }
}
```

### Model instance

To put a cache value for a model instance:

```php
Video::first()->modelCache('currentTime', 20);
Video::first()->modelCache('randomSeed', 20, now()->addDay()); // cache for one day
```

To retrieve a cached model instance value:

```php
Video::first()->modelCached('currentTime');
Video::first()->modelCached('randomSeed', $default); // with fallback
```

To validate if a cached model instance value exists:

```php
$model = Video::findOrFail(10);

if (! $model->hasModelCache('currentTime')) {
    $model->modelCache('currentTime', 20);
}

return $model->modelCached('currentTime');
```

To forget a cached model value:

```php
Video::first()->modelCacheForget('currentTime');
Video::first()->modelCacheForget('randomSeed');
```

### Model caching (global)

To put a model cache value based on its class:

```php
Video::setModelCache('randomSeed', 0.1);
Video::setModelCache('randomSeed', 0.1, now()->addDay()); // cache for one day
```

To retrieve a model class cached value:

```php
Video::getModelCache('randomSeed');
Video::getModelCache('randomSeed', $default);
```

To validate if a model class cached value exists:

```php
Video::hasModelCache('randomSeed');
```

To forget a model class cached value:

```php
Video::forgetModelCache('randomSeed');
```

### Creating a custom cache profile

To determine which values should be cached, a cache profile class is used. The default class that handles these questions is `Foxws\ModelCache\CacheProfiles\CacheAllSuccessful`.

You can create your own cache profile class by implementing the  `Foxws\ModelCache\CacheProfile\CacheProfile`, and overruling the `cache_profile` in `config/modelcache.php`.

It is also possible to overrule the cache prefix using the model instance. For this create a method named `cacheNameSuffix` on the model instance:

```php
use Foxws\ModelCache\Concerns\InteractsWithModelCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Video extends Model
{
    use InteractsWithModelCache;

    protected function cacheNameSuffix(string $key): string
    {
        // return Auth::check()
        //     ? (string) Auth::id()
        //     : '';

        // return "{$key}:{$this->getMorphClass()}";

        return ''; // do not use a separate cache for users
    }
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

This package is entirely based on the [space/laravel-responsecache](https://github.com/spatie/laravel-responsecache/) package.

Please consider to sponsor Spatie, such as purchasing their excellent courses. :)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

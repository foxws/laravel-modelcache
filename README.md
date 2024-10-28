# Laravel model cache helper

[![Latest Version on Packagist](https://img.shields.io/packagist/v/foxws/laravel-modelcache.svg?style=flat-square)](https://packagist.org/packages/foxws/laravel-modelcache)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/foxws/laravel-modelcache/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/foxws/laravel-modelcache/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/foxws/laravel-modelcache/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/foxws/laravel-modelcache/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/foxws/laravel-modelcache.svg?style=flat-square)](https://packagist.org/packages/foxws/laravel-modelcache)

This package does not cache models, it gives you helpers to manage the Laravel Cache using a model. By default, logged in users will each have their own separate cache prefix.

## Installation

You can install the package via composer:

```bash
composer require foxws/laravel-modelcache
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="modelcache-config"
```

## Usage

Implement the `Foxws\ModelCache\Concerns\InteractsWithModelCache` trait to your Eloquent models:

```php
use Foxws\ModelCache\Concerns\InteractsWithModelCache;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use InteractsWithModelCache;
}

```

```php
use Foxws\ModelCache\Concerns\InteractsWithModelCache;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use InteractsWithModelCache;
}

```

To cache a model value:

```php
User::first()->modelCache('randomSeed', 0.5);
Video::first()->modelCache('currentTime', 20, now()->addDay()); // cache for one day
```

To get a cached value:

```php
User::first()->modelCached('randomSeed');
Video::first()->modelCached('currentTime', $default); // with fallback
```

To forget a value:

```php
Video::first()->modelCacheForget('currentTime');
```

### Creating a custom cache profile

To determine which values should be cached, and for how long, a cache profile class is used. The default class that handles these questions is `Foxws\ModelCache\CacheProfiles\CacheAllSuccessful`.

You can create your own cache profile class by implementing the  `Foxws\ModelCache\CacheProfile\CacheProfile`, and overruling the `cache_profile` in `config/modelcache.php`.

For example you could create a cache profile that only caches when an user is authenticated.

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

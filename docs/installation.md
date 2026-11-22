---
section: Getting Started
order: 1
---

# Installation

Install the package with Composer:

```bash
composer require foxws/laravel-modelcache
```

Then, optionally, publish the config file:

```bash
php artisan vendor:publish --tag="modelcache-config"
```

## Environment variables

| Variable               | Default           | Description                                                   |
| ---------------------- | ----------------- | --------------------------------------------------------------- |
| `MODEL_CACHE_ENABLED`  | `true`            | Turns caching on or off globally                               |
| `MODEL_CACHE_STORE`    | `CACHE_STORE`     | Which cache store to use (any store from `config/cache.php`)   |
| `MODEL_CACHE_LIFETIME` | `604800` (1 week) | How long a cached value lasts, in seconds                      |

## Caching objects

Cached values are only unserialized into the classes that
`serializable_classes` in `config/modelcache.php` allows. It's `null` by
default, which follows `cache.serializable_classes`. New Laravel
applications set that to `false`, so list the classes of any objects you
cache, such as dates and enums:

```php
'serializable_classes' => [Illuminate\Support\Carbon::class, App\Enums\Quality::class],
```

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
`serializable_classes` in `config/modelcache.php` allows, so whoever can
write to the cache store can't create other objects. By default those are
the date classes: `Carbon`, `CarbonImmutable`, `Illuminate\Support\Carbon`,
`DateTime` and `DateTimeImmutable`. Enums always come back, as PHP doesn't
restrict them. Other objects come back as `__PHP_Incomplete_Class`.

To cache other objects, add their classes to the list. Only add classes
whose unserializing can't run code or create other objects: not models,
collections, `CarbonPeriod` or `CarbonInterval`.

```php
'serializable_classes' => [
    Carbon\Carbon::class,
    Carbon\CarbonImmutable::class,
    Illuminate\Support\Carbon::class,
    DateTime::class,
    DateTimeImmutable::class,
    App\Support\Money::class,
],
```

Set it to `null` to use `cache.serializable_classes` instead (no classes
when that isn't set either), or to `true` to allow every class.

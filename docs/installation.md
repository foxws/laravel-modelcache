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

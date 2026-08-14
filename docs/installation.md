---
sidebar_position: 2
---

# Installation

Install the package via Composer:

```bash
composer require foxws/laravel-modelcache
```

Optionally publish the config file:

```bash
php artisan vendor:publish --tag="modelcache-config"
```

## Environment variables

| Variable               | Default           | Description                                            |
| ----------------------- | ------------------ | -------------------------------------------------------- |
| `MODEL_CACHE_ENABLED`  | `true`            | Toggle caching on/off globally                         |
| `MODEL_CACHE_STORE`    | `CACHE_STORE`     | Cache store to use (any store from `config/cache.php`) |
| `MODEL_CACHE_LIFETIME` | `604800` (1 week) | Default TTL in seconds                                 |

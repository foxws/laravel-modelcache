# Laravel model cache helper

[![Latest Version on Packagist](https://img.shields.io/packagist/v/foxws/laravel-modelcache.svg?style=flat-square)](https://packagist.org/packages/foxws/laravel-modelcache)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/foxws/laravel-modelcache/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/foxws/laravel-modelcache/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/foxws/laravel-modelcache/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/foxws/laravel-modelcache/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/foxws/laravel-modelcache.svg?style=flat-square)](https://packagist.org/packages/foxws/laravel-modelcache)

Attach arbitrary cached values to Eloquent model instances or classes using any Laravel cache driver. By default each authenticated user gets an isolated cache namespace, so two users never share the same cached value for the same model.

## Installation

Install the package via Composer:

```bash
composer require foxws/laravel-modelcache
```

Optionally publish the config file:

```bash
php artisan vendor:publish --tag="modelcache-config"
```

### Environment variables

| Variable               | Default           | Description                                            |
| ---------------------- | ----------------- | ------------------------------------------------------ |
| `MODEL_CACHE_ENABLED`  | `true`            | Toggle caching on/off globally                         |
| `MODEL_CACHE_STORE`    | `CACHE_STORE`     | Cache store to use (any store from `config/cache.php`) |
| `MODEL_CACHE_LIFETIME` | `604800` (1 week) | Default TTL in seconds                                 |

## Usage

### 1. Add the trait to your model

```php
use Foxws\ModelCache\Concerns\InteractsWithModelCache;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use InteractsWithModelCache;
}
```

That is all the setup required. Every public method below becomes available on the model.

---

### 2. Instance cache

These methods are scoped to a specific model record (e.g. `Video` with `id = 5`).

**Store a value:**

```php
$video = Video::findOrFail(5);

$video->modelCache('playback_position', 142);

// With a custom TTL
$video->modelCache('random_seed', 0.73, now()->addHours(6));
$video->modelCache('last_viewed', now(), 3600); // TTL as seconds
```

**Retrieve a value:**

```php
$position = $video->modelCached('playback_position');        // null if not cached
$seed     = $video->modelCached('random_seed', 0.5);         // 0.5 as fallback
```

**Check existence:**

```php
if (! $video->modelCacheHas('playback_position')) {
    $video->modelCache('playback_position', 0);
}
```

**Forget a value:**

```php
$video->modelCacheForget('playback_position');
```

**Practical example — lazy-load an expensive computed value:**

```php
class VideoController extends Controller
{
    public function show(Video $video): JsonResponse
    {
        if (! $video->modelCacheHas('stats')) {
            $stats = $this->computeExpensiveStats($video);
            $video->modelCache('stats', $stats, now()->addDay());
        }

        return response()->json($video->modelCached('stats'));
    }
}
```

---

### 3. Class cache (global)

These static methods are scoped to the model _class_ rather than a specific record. Useful for values shared across all instances, such as global seeds or configuration.

**Store a value:**

```php
Video::setModelCache('random_seed', 0.42);
Video::setModelCache('random_seed', 0.42, now()->addWeek());
```

**Retrieve a value:**

```php
$seed = Video::getModelCache('random_seed');          // null if not cached
$seed = Video::getModelCache('random_seed', 0.5);     // with fallback
```

**Check existence:**

```php
if (! Video::hasModelCache('random_seed')) {
    Video::setModelCache('random_seed', rand() / getrandmax());
}
```

**Forget a value:**

```php
Video::forgetModelCache('random_seed');
```

---

### 4. Facade

Use the `ModelCache` facade when you need to interact with caching outside of a model — for example in an action class or a service.

```php
use Foxws\ModelCache\Facades\ModelCache;

class RecordPlaybackPosition
{
    public function handle(Video $video, int $seconds): void
    {
        if (! ModelCache::enabled()) {
            return;
        }

        if (! ModelCache::shouldCache($video, 'playback_position', $seconds)) {
            return;
        }

        ModelCache::cache($video, 'playback_position', $seconds, now()->addDay());
    }
}
```

```php
// Read from cache
$position = ModelCache::getCachedValue($video, 'playback_position');

// Check
$exists = ModelCache::hasBeenCached($video, 'playback_position');

// Forget one or multiple keys
ModelCache::forget($video, 'playback_position');
ModelCache::forget($video, ['playback_position', 'random_seed']);
```

---

### 5. Controlling which values get cached (`shouldModelCache`)

Override `shouldModelCache` on the model to conditionally skip caching certain keys or values:

```php
class Video extends Model
{
    use InteractsWithModelCache;

    public function shouldModelCache(string $key, mixed $value = null): bool
    {
        // Never cache a null value
        if ($value === null) {
            return false;
        }

        // Only cache specific keys
        if (! in_array($key, ['playback_position', 'random_seed', 'stats'])) {
            return false;
        }

        return true;
    }
}
```

---

### 6. Custom cache profile

A cache profile controls global caching behaviour: whether caching is enabled, when values expire, and which per-user namespace suffix to use. The default is `CacheAllSuccessful`, which caches all values for all users.

Create your own by implementing `CacheProfile`:

```php
use Foxws\ModelCache\CacheProfiles\BaseCacheProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuthenticatedUserCacheProfile extends BaseCacheProfile
{
    public function shouldUseCache(Model $model, string $key): bool
    {
        // Only cache for authenticated users
        return Auth::check();
    }

    public function shouldCacheValue(mixed $value = null): bool
    {
        // Do not cache null or empty strings
        return $value !== null && $value !== '';
    }
}
```

Register it in `config/modelcache.php`:

```php
'cache_profile' => AuthenticatedUserCacheProfile::class,
```

---

### 7. Per-model cache namespace (`cacheNameSuffix`)

By default, `BaseCacheProfile::useCacheNameSuffix` returns the authenticated user's ID, isolating each user's cache. You can override this per model:

```php
class Video extends Model
{
    use InteractsWithModelCache;

    protected function cacheNameSuffix(string $key): string
    {
        // Shared cache regardless of who is logged in
        return '';
    }
}
```

```php
class Post extends Model
{
    use InteractsWithModelCache;

    protected function cacheNameSuffix(string $key): string
    {
        // Separate cache per user
        return Auth::check() ? (string) Auth::id() : '';
    }
}
```

```php
class Report extends Model
{
    use InteractsWithModelCache;

    protected function cacheNameSuffix(string $key): string
    {
        // Separate cache per key type, shared across users
        return $key;
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

This package is heavily inspired by and based on the [spatie/laravel-responsecache](https://github.com/spatie/laravel-responsecache/) package by [Spatie](https://spatie.be).

If you find their work valuable, please consider [sponsoring Spatie](https://spatie.be/open-source/support-us) or purchasing one of their [courses and products](https://spatie.be/products).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---
sidebar_position: 5
---

# Customization

## Controlling which values get cached (`shouldModelCache`)

Override `shouldModelCache` on the model to conditionally skip caching
certain keys or values:

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

## Custom cache profile

A cache profile controls global caching behaviour: whether caching is
enabled, when values expire, and which per-user namespace suffix to use.
The default is `CacheAllSuccessful`, which caches all values for all
users.

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

## Per-model cache namespace (`cacheNameSuffix`)

By default, `BaseCacheProfile::useCacheNameSuffix` returns the
authenticated user's ID, isolating each user's cache. You can override
this per model:

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

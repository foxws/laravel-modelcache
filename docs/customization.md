---
section: Configuration
order: 1
---

# Customization

## Controlling which values get cached (`shouldModelCache`)

Override `shouldModelCache` on the model to skip caching for certain
keys or values:

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

A cache profile controls caching behavior overall: whether it's
enabled, when values expire, and what namespace suffix separates users.
The default, `CacheAllSuccessful`, caches every value for every user.

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

By default, `BaseCacheProfile::useCacheNameSuffix` returns the logged-in
user's ID, which keeps each user's cache separate. You can override this
per model:

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

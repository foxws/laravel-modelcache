---
sidebar_position: 4
---

# Facade

Use the `ModelCache` facade when you need to interact with caching outside
of a model — for example in an action class or a service.

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

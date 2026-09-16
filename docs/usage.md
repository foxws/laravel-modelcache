---
section: Usage
order: 1
---

# Usage

## Add the trait to your model

```php
use Foxws\ModelCache\Concerns\InteractsWithModelCache;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use InteractsWithModelCache;
}
```

That's all the setup you need — every method below is now available on
the model.

## Instance cache

These methods target one specific record, e.g. a `Video` with
`id = 5`.

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

**Remember a value (fetch or store):**

`modelCacheRemember` returns the cached value if there is one. Otherwise
it resolves the closure (or uses the plain value you pass), stores the
result, and returns it.

```php
// With a closure (recommended for expensive operations)
$stats = $video->modelCacheRemember('stats', fn() => $this->computeExpensiveStats($video), now()->addDay());

// With a plain value
$video->modelCacheRemember('random_seed', 0.73, now()->addHours(6));
```

**Practical example — lazy-load an expensive computed value:**

```php
class VideoController extends Controller
{
    public function show(Video $video): JsonResponse
    {
        $stats = $video->modelCacheRemember('stats', fn() => $this->computeExpensiveStats($video), now()->addDay());

        return response()->json($stats);
    }
}
```

## Class cache (global)

These static methods target the model _class_ rather than one record.
Use them for values shared across every instance, such as a global seed
or a piece of configuration.

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

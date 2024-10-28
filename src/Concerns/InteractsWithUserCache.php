<?php

namespace Foxws\ModelCache\Concerns;

use Foxws\ModelCache\Facades\ModelCache;

trait InteractsWithModelCache
{
    public function cacheStore(string $key, mixed $value = null): mixed
    {
        if (! ModelCache::shouldCache($key, $value)) {
            return null;
        }

        return ModelCache::cache($this, $key, $value);
    }

    public function cacheStored(string $key): mixed
    {
        return ModelCache::getCachedValue($this, $key);
    }
}

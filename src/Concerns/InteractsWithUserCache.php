<?php

namespace Foxws\UserCache\Concerns;

use Foxws\UserCache\Facades\UserCache;

trait InteractsWithUserCache
{
    public function cacheStore(string $key, mixed $value = null): mixed
    {
        if (! UserCache::shouldCache($key, $value)) {
            return null;
        }

        return UserCache::cache($this, $key, $value);
    }

    public function cacheStored(string $key): mixed
    {
        return UserCache::getCachedValue($this, $key);
    }
}

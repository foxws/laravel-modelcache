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

        return UserCache::cache($key, $value);
    }

    public function cacheStored(string $key, mixed $value = null): mixed
    {
        return UserCache::getCachedValue($key, $value);
    }
}
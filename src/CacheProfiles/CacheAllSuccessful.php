<?php

namespace Foxws\ModelCache\CacheProfiles;

class CacheAllSuccessful extends BaseCacheProfile
{
    public function shouldUseCache(string $key): bool
    {
        return true;
    }

    public function shouldCacheValue(mixed $value = null): bool
    {
        return true;
    }
}

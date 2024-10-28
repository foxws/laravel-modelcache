<?php

namespace Foxws\ModelCache\CacheProfiles;

use Illuminate\Database\Eloquent\Model;

class CacheAllSuccessful extends BaseCacheProfile
{
    public function shouldUseCache(Model $model, string $key): bool
    {
        return true;
    }

    public function shouldCacheValue(mixed $value = null): bool
    {
        return true;
    }
}

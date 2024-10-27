<?php

namespace Foxws\UserCache\CacheProfiles;

use Illuminate\Support\Facades\Auth;

class CacheAllSuccessful extends BaseCacheProfile
{
    public function shouldUseCache(string $key): bool
    {
        return Auth::check();
    }

    public function shouldCacheValue(mixed $value = null): bool
    {
        return true;
    }
}

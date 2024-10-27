<?php

namespace Foxws\UserCache\CacheProfiles;

use Illuminate\Foundation\Auth\User;

class CacheAllSuccessful extends BaseCacheProfile
{
    public function shouldUseCache(User $user): bool
    {
        return true;
    }

    public function shouldCacheData(mixed $data): bool
    {
        return true;
    }
}

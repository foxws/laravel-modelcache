<?php

namespace Foxws\UserCache\Hasher;

use Foxws\UserCache\CacheProfiles\CacheProfile;
use Illuminate\Foundation\Auth\User;

class DefaultHasher implements CacheHasher
{
    public function __construct(
        protected CacheProfile $cacheProfile,
    ) {
        //
    }

    public function getHashFor(User $user, string $key): string
    {
        $cacheNameSuffix = $this->getCacheNameSuffix($key);

        return 'usercache-' . hash(
            'xxh128',
            "{$user->getKey()}:{$key}:{$cacheNameSuffix}"
        );
    }

    protected function getCacheNameSuffix(string $key)
    {
        return $this->cacheProfile->useCacheNameSuffix($key);
    }
}

<?php

namespace Foxws\UserCache\Hasher;

use Illuminate\Foundation\Auth\User;
use Foxws\UserCache\CacheProfiles\CacheProfile;

class DefaultHasher implements EloquentHasher
{
    public function __construct(
        protected CacheProfile $cacheProfile,
    ) {
        //
    }

    public function getHashFor(User $user): string
    {
        $cacheNameSuffix = $this->getCacheNameSuffix($user);

        return 'usercache-' . hash(
            'xxh128',
            "{$this->getNormalizedCacheKey($user)}:{$cacheNameSuffix}"
        );
    }

    protected function getNormalizedCacheKey(User $user): string
    {
        return "{$user->getMorphClass()}:{$user->getKey()}";
    }

    protected function getCacheNameSuffix(User $user)
    {
        return $this->cacheProfile->useCacheNameSuffix($user);
    }
}

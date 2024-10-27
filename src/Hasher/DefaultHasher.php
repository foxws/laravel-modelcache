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

    public function getHashFor(User $user, mixed $value): string
    {
        $cacheNameSuffix = $this->getCacheNameSuffix($user);

        return 'usercache-'.hash(
            'xxh128',
            "{$cacheNameSuffix}:{$this->getNormalizedCacheValue($value)}"
        );
    }

    protected function getNormalizedCacheValue(mixed $value): mixed
    {
        return $value;
    }

    protected function getCacheNameSuffix(User $user)
    {
        return $this->cacheProfile->useCacheNameSuffix($user);
    }
}

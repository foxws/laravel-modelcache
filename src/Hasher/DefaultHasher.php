<?php

namespace Foxws\UserCache\Hasher;

use Foxws\UserCache\CacheProfiles\CacheProfile;

class DefaultHasher implements CacheHasher
{
    public function __construct(
        protected CacheProfile $cacheProfile,
    ) {
        //
    }

    public function getHashFor(string $key, mixed $value = null): string
    {
        $cacheNameSuffix = $this->getCacheNameSuffix($key);

        return 'usercache-'.hash(
            'xxh128',
            "{$key}:{$cacheNameSuffix}"
        );
    }

    protected function getCacheNameSuffix(string $key)
    {
        return $this->cacheProfile->useCacheNameSuffix($key);
    }
}

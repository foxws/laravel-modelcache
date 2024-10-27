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

    public function getHashFor(mixed $value): string
    {
        $cacheNameSuffix = $this->getCacheNameSuffix($value);

        return 'usercache-' . hash(
            'xxh128',
            "{$this->getNormalizedCacheValue($value)}:{$cacheNameSuffix}"
        );
    }

    protected function getNormalizedCacheValue(mixed $value): mixed
    {
        return $value;
    }

    protected function getCacheNameSuffix(mixed $value)
    {
        return $this->cacheProfile->useCacheNameSuffix($value);
    }
}

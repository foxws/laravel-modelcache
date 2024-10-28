<?php

namespace Foxws\ModelCache\CacheProfiles;

use DateTime;

interface CacheProfile
{
    public function enabled(): bool;

    public function shouldUseCache(string $key): bool;

    public function shouldCacheValue(mixed $value = null): bool;

    public function cacheValueUntil(string $key): DateTime;

    /*
     * Return a string to differentiate this request from others.
     *
     * For example: if you want a different cache per user you could return the id of
     * the logged in user.
     */
    public function useCacheNameSuffix(string $key): string;
}

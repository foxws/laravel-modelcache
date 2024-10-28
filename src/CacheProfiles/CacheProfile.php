<?php

namespace Foxws\ModelCache\CacheProfiles;

use DateTime;
use Illuminate\Database\Eloquent\Model;

interface CacheProfile
{
    public function enabled(): bool;

    public function shouldUseCache(Model $model, string $key): bool;

    public function shouldCacheValue(mixed $value = null): bool;

    public function cacheValueUntil(Model $model, string $key): DateTime;

    /*
     * Return a string to differentiate this request from others.
     *
     * For example: if you want a different cache per user you could return the id of
     * the logged in user.
     */
    public function useCacheNameSuffix(Model $model, string $key): string;
}

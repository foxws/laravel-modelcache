<?php

namespace Foxws\UserCache\CacheProfiles;

use DateTime;
use Illuminate\Foundation\Auth\User;

interface CacheProfile
{
    public function enabled(mixed $value): bool;

    public function shouldUseCache(User $user): bool;

    public function shouldCacheValue(mixed $value): bool;

    public function cacheValueUntil(mixed $value): DateTime;

    /*
     * Return a string to differentiate this request from others.
     *
     * For example: if you want a different cache per user you could return the id of
     * the logged in user.
     */
    public function useCacheNameSuffix(mixed $value): string;
}

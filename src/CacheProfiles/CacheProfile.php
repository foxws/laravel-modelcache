<?php

namespace Foxws\UserCache\CacheProfiles;

use DateTime;
use Illuminate\Foundation\Auth\User;

interface CacheProfile
{
    public function enabled(User $user): bool;

    public function shouldUseCache(User $user): bool;

    public function shouldCacheData(mixed $data): bool;

    public function cacheUntil(User $user): DateTime;

    /*
     * Return a string to differentiate this request from others.
     *
     * For example: if you want a different cache per user you could return the id of
     * the logged in user.
     */
    public function useCacheNameSuffix(User $user): string;
}

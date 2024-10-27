<?php

namespace Foxws\UserCache\CacheProfiles;

use DateTime;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

abstract class BaseCacheProfile implements CacheProfile
{
    public function enabled(User $user): bool
    {
        return config('usercache.enabled');
    }

    public function cacheUntil(User $user): DateTime
    {
        return Carbon::now()->addSeconds(
            config('usercache.cache_lifetime_in_seconds')
        );
    }

    public function useCacheNameSuffix(User $user): string
    {
        return Auth::check()
            ? (string) $user->getKey()
            : '';
    }

    public function isRunningInConsole(): bool
    {
        if (app()->environment('testing')) {
            return false;
        }

        return app()->runningInConsole();
    }
}

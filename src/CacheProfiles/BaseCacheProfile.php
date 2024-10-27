<?php

namespace Foxws\UserCache\CacheProfiles;

use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

abstract class BaseCacheProfile implements CacheProfile
{
    public function enabled(mixed $value): bool
    {
        return config('usercache.enabled');
    }

    public function cacheValueUntil(mixed $value): DateTime
    {
        return Carbon::now()->addSeconds(
            config('usercache.cache_lifetime_in_seconds')
        );
    }

    public function useCacheNameSuffix(mixed $value): string
    {
        return Auth::check()
            ? (string) Auth::id()
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

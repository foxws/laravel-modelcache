<?php

namespace Foxws\ModelCache\CacheProfiles;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

abstract class BaseCacheProfile implements CacheProfile
{
    public function enabled(): bool
    {
        return config('modelcache.enabled');
    }

    public function cacheValueUntil(Model $model, string $key): DateTime
    {
        return Carbon::now()->addSeconds(
            config('modelcache.cache_lifetime_in_seconds')
        );
    }

    public function useCacheNameSuffix(Model $model, string $key): string
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

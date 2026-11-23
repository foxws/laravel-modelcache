<?php

declare(strict_types=1);

namespace Foxws\ModelCache\CacheProfiles;

use DateTimeInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

abstract class BaseCacheProfile implements CacheProfile
{
    public function enabled(): bool
    {
        return Config::boolean('modelcache.enabled');
    }

    public function cacheValueUntil(Model $model, string $key): DateTimeInterface
    {
        return Carbon::now()->addSeconds(
            Config::integer('modelcache.cache_lifetime_in_seconds')
        );
    }

    public function useCacheNameSuffix(Model $model, string $key): string
    {
        $user = Auth::user();

        return $user !== null ? static::suffixFor($user) : '';
    }

    /**
     * The namespace of a user. An ID alone isn't enough, as admins and users can share one. The guard
     * isn't part of it for models, as one user can sign in through several, e.g. web and sanctum.
     */
    public static function suffixFor(Authenticatable $user): string
    {
        $source = $user instanceof Model
            ? [(string) $user->getConnection()->getName(), $user->getConnection()->getDatabaseName(), $user->getTable()]
            : [Auth::getDefaultDriver()];

        return implode('|', [$user::class, ...$source, (string) $user->getAuthIdentifier()]);
    }

    public function isRunningInConsole(): bool
    {
        if (app()->environment('testing')) {
            return false;
        }

        return app()->runningInConsole();
    }
}

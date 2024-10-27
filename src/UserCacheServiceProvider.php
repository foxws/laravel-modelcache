<?php

namespace Foxws\UserCache;

use Foxws\UserCache\CacheProfiles\CacheProfile;
use Foxws\UserCache\Commands\ClearCommand;
use Foxws\UserCache\Hasher\CacheHasher;
use Foxws\UserCache\Serializers\Serializer;
use Illuminate\Cache\Repository;
use Illuminate\Container\Container;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class UserCacheServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-usercache')
            ->hasConfigFile('usercache')
            ->hasCommands([
                ClearCommand::class,
            ]);
    }

    public function packageBooted()
    {
        $this->app->bind(CacheProfile::class, function (Container $app) {
            return $app->make(config('usercache.cache_profile'));
        });

        $this->app->bind(CacheHasher::class, function (Container $app) {
            return $app->make(config('usercache.hasher'));
        });

        $this->app->bind(Serializer::class, function (Container $app) {
            return $app->make(config('usercache.serializer'));
        });

        $this->app->when(UserCacheRepository::class)
            ->needs(Repository::class)
            ->give(function (): Repository {
                return app('cache')->store(config('usercache.cache_store'));
            });

        $this->app->singleton('usercache', UserCache::class);
    }
}

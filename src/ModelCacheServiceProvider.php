<?php

declare(strict_types=1);

namespace Foxws\ModelCache;

use Foxws\ModelCache\CacheProfiles\CacheProfile;
use Foxws\ModelCache\Hasher\CacheHasher;
use Foxws\ModelCache\Serializers\Serializer;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Config;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ModelCacheServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-modelcache')
            ->hasConfigFile('modelcache');
    }

    public function packageBooted()
    {
        $this->app->bind(CacheProfile::class, function (Container $app) {
            return $app->make(Config::string('modelcache.cache_profile'));
        });

        $this->app->bind(CacheHasher::class, function (Container $app) {
            return $app->make(Config::string('modelcache.hasher'));
        });

        $this->app->bind(Serializer::class, function (Container $app) {
            return $app->make(Config::string('modelcache.serializer'));
        });

        $this->app->when(ModelCacheRepository::class)
            ->needs(Repository::class)
            ->give(function (): Repository {
                return app('cache')->store(Config::string('modelcache.cache_store'));
            });

        $this->app->scoped('modelcache', ModelCache::class);
    }
}

<?php

use Foxws\UserCache\Events\ClearedUserCache;
use Foxws\UserCache\Events\ClearingUserCache;
use Foxws\UserCache\UserCacheRepository;
use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->createTaggableResponseCacheStore = function (): Repository {
        // Simulating construction of Repository inside of the service provider
        return $this->app->contextual[UserCacheRepository::class][$this->app->getAlias(Repository::class)]();
    };
});

it('will fire events when clearing the cache', function () {
    Event::fake();

    Artisan::call('usercache:clear');

    Event::assertDispatched(ClearingUserCache::class);
    Event::assertDispatched(ClearedUserCache::class);
});

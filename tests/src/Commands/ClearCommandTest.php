<?php

use Foxws\UserCache\Tests\TestCase;
use Foxws\UserCache\UserCacheRepository;
use Illuminate\Cache\Repository;

beforeEach(function () {
    $this->createTaggableResponseCacheStore = function (): Repository {
        // Simulating construction of Repository inside of the service provider
        return $this->app->contextual[UserCacheRepository::class][$this->app->getAlias(Repository::class)]();
    };
});

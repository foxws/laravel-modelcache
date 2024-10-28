<?php

use Foxws\ModelCache\Tests\TestCase;
use Foxws\ModelCache\ModelCacheRepository;
use Illuminate\Cache\Repository;

uses(TestCase::class);

beforeEach(function () {
    $this->createTaggableResponseCacheStore = function (): Repository {
        // Simulating construction of Repository inside of the service provider
        return $this->app->contextual[ModelCacheRepository::class][$this->app->getAlias(Repository::class)]();
    };
});

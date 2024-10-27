<?php

use Foxws\UserCache\CacheProfiles\CacheAllSuccessful;

use function PHPUnit\Framework\assertTrue;

beforeEach(function () {
    $this->cacheProfile = app(CacheAllSuccessful::class);
});

it('will determine that values should be cached', function () {
    assertTrue($this->cacheProfile->shouldCacheValue('foo'));
    assertTrue($this->cacheProfile->shouldCacheValue(['foo', 'bar']));
});

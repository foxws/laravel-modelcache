<?php

use Foxws\UserCache\CacheProfiles\CacheAllSuccessful;
use Foxws\UserCache\Tests\Models\User;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

beforeEach(function () {
    $this->cacheProfile = app(CacheAllSuccessful::class);
});

it('will determine that values should be cached', function () {
    assertTrue($this->cacheProfile->shouldCacheValue('foo'));
    assertTrue($this->cacheProfile->shouldCacheValue(['foo', 'bar']));
});

it('will use the id of the logged in user to differentiate caches', function () {
    assertEquals('', $this->cacheProfile->useCacheNameSuffix('foo'));

    User::all()->map(function ($user) {
        auth()->login(User::find($user->getKey()));
        assertEquals($user->getKey(), $this->cacheProfile->useCacheNameSuffix('foo'));
    });
});

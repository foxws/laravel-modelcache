<?php

use Foxws\UserCache\CacheProfiles\CacheAllSuccessful;
use Foxws\UserCache\Tests\Models\User;
use Foxws\UserCache\Tests\TestCase;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

uses(TestCase::class);

beforeEach(function () {
    $this->cacheProfile = app(CacheAllSuccessful::class);
});

it('will determine that values should be cached', function () {
    assertTrue($this->cacheProfile->shouldCacheValue('testValue'));
    assertTrue($this->cacheProfile->shouldCacheValue(10));
    assertTrue($this->cacheProfile->shouldCacheValue(['testValue', 'foo', 'bar', 10]));
});

it('will use the id of the logged in user to differentiate caches', function () {
    assertEquals('', $this->cacheProfile->useCacheNameSuffix('foo'));

    User::all()->map(function ($user) {
        Auth::loginUsingId(User::find($user->getKey()));
        assertEquals($user->getKey(), $this->cacheProfile->useCacheNameSuffix('testValue'));
    });
});

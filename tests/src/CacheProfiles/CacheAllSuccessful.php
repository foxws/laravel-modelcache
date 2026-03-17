<?php

use Foxws\ModelCache\CacheProfiles\CacheAllSuccessful;
use Foxws\ModelCache\Tests\Models\User;
use Foxws\ModelCache\Tests\TestCase;
use Illuminate\Support\Facades\Auth;

uses(TestCase::class);

beforeEach(function () {
    /** @var \Foxws\ModelCache\CacheProfiles\CacheAllSuccessful $cacheProfile */
    $this->cacheProfile = app(CacheAllSuccessful::class);
});

it('will determine that values should be cached', function () {
    expect($this->cacheProfile->shouldCacheValue('testValue'))->toBeTrue();
    expect($this->cacheProfile->shouldCacheValue(10))->toBeTrue();
    expect($this->cacheProfile->shouldCacheValue(['testValue', 'foo', 'bar', 10]))->toBeTrue();
});

it('will use the id of the logged in user to differentiate caches', function () {
    expect($this->cacheProfile->useCacheNameSuffix('foo'))->toBe('');

    User::all()->map(function ($user) {
        Auth::loginUsingId(User::find($user->getKey()));
        expect($this->cacheProfile->useCacheNameSuffix('testValue'))->toBe($user->getKey());
    });
});

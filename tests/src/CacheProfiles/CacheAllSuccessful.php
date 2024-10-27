<?php

use Illuminate\Http\JsonResponse;

use Foxws\UserCache\CacheProfiles\CacheAllSuccessful;
use Foxws\UserCache\Tests\Models\User;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

beforeEach(function () {
    $this->cacheProfile = app(CacheAllSuccessful::class);
});

it('will determine that values should be cached', function () {
    assertTrue($this->cacheProfile->shouldCacheValue('foo'));
    assertTrue($this->cacheProfile->shouldCacheValue(['foo', 'bar']));
});

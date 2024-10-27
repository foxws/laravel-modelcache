<?php

use Foxws\UserCache\CacheProfiles\CacheProfile;
use Foxws\UserCache\Hasher\DefaultHasher;
use Foxws\UserCache\Tests\Models\User;
use Foxws\UserCache\Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

uses(TestCase::class);

beforeEach(function () {
    $this->cacheProfile = Mockery::mock(CacheProfile::class);

    $this->cacheHasher = new DefaultHasher($this->cacheProfile);

    $this->user = User::factory()->create();
});

it('can generate a hash for a cache', function () {
    $this->cacheProfile->shouldReceive('useCacheNameSuffix')->andReturn('cacheProfileSuffix');

    assertEquals(
        'usercache-5d712697b89eb7b5e319c4faf4272cf5',
        $this->cacheHasher->getHashFor($this->user, 'last_viewed')
    );
});

it('can store value using user store', function () {
    $this->user->cacheStore('cacheKey', 'cacheValue');

    assertEquals('cacheValue', $this->user->cacheStored('cacheKey'));
});

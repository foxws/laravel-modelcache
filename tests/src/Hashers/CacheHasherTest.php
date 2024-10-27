<?php

use Foxws\UserCache\CacheProfiles\CacheProfile;
use Foxws\UserCache\Hasher\DefaultHasher;
use Foxws\UserCache\Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

uses(TestCase::class);

beforeEach(function () {
    $this->cacheProfile = Mockery::mock(CacheProfile::class);

    $this->cacheHasher = new DefaultHasher($this->cacheProfile);
});

it('can generate a hash for a request', function () {
    $this->cacheProfile->shouldReceive('useCacheNameSuffix')->andReturn('cacheProfileSuffix');

    assertEquals(
        'usercache-5d712697b89eb7b5e319c4faf4272cf5',
        $this->cacheHasher->getHashFor('last_viewed', 'foo')
    );
});

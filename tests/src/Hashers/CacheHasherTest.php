<?php

use Foxws\UserCache\CacheProfiles\CacheProfile;
use Foxws\UserCache\Hasher\DefaultHasher;

use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    $this->cacheProfile = Mockery::mock(CacheProfile::class);

    $this->cacheHasher = new DefaultHasher($this->cacheProfile);
});

it('can generate a hash for a request', function () {
    $this->cacheProfile->shouldReceive('useCacheNameSuffix')->andReturn('cacheProfileSuffix');

    assertEquals(
        'usercache-9937bec32aa1918917ad64b2b25f2982',
        $this->cacheHasher->getHashFor('foo')
    );
});

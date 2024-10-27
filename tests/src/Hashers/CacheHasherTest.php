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
        'usercache-d5a054922739f7857b5175e6fe1cab51',
        $this->cacheHasher->getHashFor('last_viewed', 'foo')
    );
});

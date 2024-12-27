<?php

use Foxws\ModelCache\CacheProfiles\CacheProfile;
use Foxws\ModelCache\Hasher\DefaultHasher;
use Foxws\ModelCache\Tests\Models\Post;
use Foxws\ModelCache\Tests\Models\User;
use Foxws\ModelCache\Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;

uses(TestCase::class);

beforeEach(function () {
    $this->cacheProfile = Mockery::mock(CacheProfile::class);

    $this->cacheHasher = new DefaultHasher($this->cacheProfile);
});

it('can generate a hash for a class', function () {
    $this->cacheProfile->shouldReceive('useCacheNameSuffix')->andReturn('cacheProfileSuffix');

    assertEquals(
        'modelcache-caf41bdf71501c06d747ec2b930d36ed',
        $this->cacheHasher->getHashFor(app(User::class), 'last_viewed')
    );

    assertEquals(
        'modelcache-bdd1645bcba416cbde602e79fd62c092',
        $this->cacheHasher->getHashFor(app(Post::class), 'last_viewed')
    );
});

it('can store values using model concern', function () {
    User::setModelCache('cacheKey', 'cacheValue');
    Post::setModelCache('cacheFoo', 'cacheBar');
    Post::setModelCache('cacheBar', 'cacheFoo', now()->addDay());

    assertEquals('cacheValue', User::getModelCache('cacheKey'));
    assertEquals('cacheBar', Post::getModelCache('cacheFoo'));
});

it('can remove values using model concern', function () {
    User::setModelCache('cacheKey', 'cacheValue');
    Post::setModelCache('cacheFoo', 'cacheBar');

    assertEquals('cacheValue', User::getModelCache('cacheKey'));
    assertEquals('cacheBar', Post::getModelCache('cacheFoo'));

    User::forgetModelCache('cacheKey');
    Post::forgetModelCache('cacheFoo');

    assertFalse(User::hasModelCache('cacheKey'));
    assertFalse(Post::hasModelCache('cacheFoo'));
});

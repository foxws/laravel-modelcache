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
    User::modelClassCache('cacheKey', 'cacheValue');
    Post::modelClassCache('cacheFoo', 'cacheBar');
    Post::modelClassCache('cacheBar', 'cacheFoo', now()->addDay());

    assertEquals('cacheValue', User::modelClassCached('cacheKey'));
    assertEquals('cacheBar', Post::modelClassCached('cacheFoo'));
});

it('can remove values using model concern', function () {
    User::modelClassCache('cacheKey', 'cacheValue');
    Post::modelClassCache('cacheFoo', 'cacheBar');

    assertEquals('cacheValue', User::modelClassCached('cacheKey'));
    assertEquals('cacheBar', Post::modelClassCached('cacheFoo'));

    User::modelClassCacheForget('cacheKey');
    Post::modelClassCacheForget('cacheFoo');

    assertFalse(User::ismodelClassCached('cacheKey'));
    assertFalse(Post::ismodelClassCached('cacheFoo'));
});

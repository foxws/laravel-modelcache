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
        'modelClassCache-7830f1c558a69446cbe74b50b6871528',
        $this->cacheHasher->getHashFor(User::class, 'last_viewed')
    );

    assertEquals(
        'modelClassCache-3f3822d73926df0c7a71519e9914ce3a',
        $this->cacheHasher->getHashFor(Post::class, 'last_viewed')
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

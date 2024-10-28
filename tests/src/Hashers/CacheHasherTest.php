<?php

use Foxws\ModelCache\CacheProfiles\CacheProfile;
use Foxws\ModelCache\Hasher\DefaultHasher;
use Foxws\ModelCache\Tests\Models\Post;
use Foxws\ModelCache\Tests\Models\User;
use Foxws\ModelCache\Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

uses(TestCase::class);

beforeEach(function () {
    $this->cacheProfile = Mockery::mock(CacheProfile::class);

    $this->cacheHasher = new DefaultHasher($this->cacheProfile);

    $this->user = User::factory()->create();
    $this->post = Post::factory()->create();
});

it('can generate a hash for a cache', function () {
    $this->cacheProfile->shouldReceive('useCacheNameSuffix')->andReturn('cacheProfileSuffix');

    assertEquals(
        'modelcache-7830f1c558a69446cbe74b50b6871528',
        $this->cacheHasher->getHashFor($this->user, 'last_viewed')
    );

    assertEquals(
        'modelcache-3f3822d73926df0c7a71519e9914ce3a',
        $this->cacheHasher->getHashFor($this->post, 'last_viewed')
    );
});

it('can store values using model concern', function () {
    $this->user->modelCache('cacheKey', 'cacheValue');
    $this->post->modelCache('cacheFoo', 'cacheBar');

    assertEquals('cacheValue', $this->user->modelCached('cacheKey'));
    assertEquals('cacheBar', $this->post->modelCached('cacheFoo'));
});

it('can remove values using model concern', function () {
    $this->user->modelCache('cacheKey', 'cacheValue');
    $this->post->modelCache('cacheFoo', 'cacheBar');

    assertEquals('cacheValue', $this->user->modelCached('cacheKey'));
    assertEquals('cacheBar', $this->post->modelCached('cacheFoo'));

    $this->user->modelCacheForget('cacheKey');
    $this->post->modelCacheForget('cacheFoo');

    assertTrue(false, $this->user->isModelCached('cacheKey'));
    assertTrue(false, $this->post->isModelCached('cacheFoo'));
});

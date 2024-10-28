<?php

use Foxws\ModelCache\CacheProfiles\CacheProfile;
use Foxws\ModelCache\Hasher\DefaultHasher;
use Foxws\ModelCache\Tests\Models\Post;
use Foxws\ModelCache\Tests\Models\User;
use Foxws\ModelCache\Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

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

it('can store values using user store', function () {
    $this->user->cacheStore('cacheKey', 'cacheValue');
    $this->post->cacheStore('cacheFoo', 'cacheBar');

    assertEquals('cacheValue', $this->user->cacheStored('cacheKey'));
    assertEquals('cacheBar', $this->post->cacheStored('cacheFoo'));
});

<?php

declare(strict_types=1);

use Foxws\ModelCache\CacheProfiles\CacheProfile;
use Foxws\ModelCache\Hasher\DefaultHasher;
use Foxws\ModelCache\Tests\Models\Post;
use Foxws\ModelCache\Tests\Models\User;
use Foxws\ModelCache\Tests\TestCase;
use Mockery\MockInterface;

uses(TestCase::class);

beforeEach(function () {
    /** @var CacheProfile&MockInterface $cacheProfile */
    $this->cacheProfile = Mockery::mock(CacheProfile::class);

    $this->cacheHasher = new DefaultHasher($this->cacheProfile);

    $this->user = User::factory()->create();
    $this->post = Post::factory()->create();
});

it('can generate a hash for a cache', function () {
    $this->cacheProfile->shouldReceive('useCacheNameSuffix')->andReturn('cacheProfileSuffix');

    expect($this->cacheHasher->getHashFor($this->user, 'last_viewed'))
        ->toBe('modelcache-cdcf01a752621af0e77e5494863945ea');

    expect($this->cacheHasher->getHashFor($this->post, 'last_viewed'))
        ->toBe('modelcache-bfc63607896ed70d8e5ca4e166dd0e42');
});

it('can store values using model concern', function () {
    $this->user->modelCache('cacheKey', 'cacheValue');
    $this->post->modelCache('cacheFoo', 'cacheBar');
    $this->post->modelCache('cacheBar', 'cacheFoo', now()->addDay());

    expect($this->user->modelCached('cacheKey'))->toBe('cacheValue');
    expect($this->post->modelCached('cacheFoo'))->toBe('cacheBar');
});

it('can remove values using model concern', function () {
    $this->user->modelCache('cacheKey', 'cacheValue');
    $this->post->modelCache('cacheFoo', 'cacheBar');

    expect($this->user->modelCached('cacheKey'))->toBe('cacheValue');
    expect($this->post->modelCached('cacheFoo'))->toBe('cacheBar');

    $this->user->modelCacheForget('cacheKey');
    $this->post->modelCacheForget('cacheFoo');

    expect($this->user->modelCacheHas('cacheKey'))->toBeFalse();
    expect($this->post->modelCacheHas('cacheFoo'))->toBeFalse();
});

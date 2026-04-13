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
});

it('can generate a hash for a class', function () {
    $this->cacheProfile->shouldReceive('useCacheNameSuffix')->andReturn('cacheProfileSuffix');

    expect($this->cacheHasher->getHashFor(app(User::class), 'last_viewed'))
        ->toBe('modelcache-a068e88c3c41b1b0fe39aafdcd6e4736');

    expect($this->cacheHasher->getHashFor(app(Post::class), 'last_viewed'))
        ->toBe('modelcache-d585a48578be8e4b9dd0f9d48051bf85');
});

it('can store values using model concern', function () {
    User::setModelCache('cacheKey', 'cacheValue');
    Post::setModelCache('cacheFoo', 'cacheBar');
    Post::setModelCache('cacheBar', 'cacheFoo', now()->addDay());

    expect(User::getModelCache('cacheKey'))->toBe('cacheValue');
    expect(Post::getModelCache('cacheFoo'))->toBe('cacheBar');
});

it('can remove values using model concern', function () {
    User::setModelCache('cacheKey', 'cacheValue');
    Post::setModelCache('cacheFoo', 'cacheBar');

    expect(User::getModelCache('cacheKey'))->toBe('cacheValue');
    expect(Post::getModelCache('cacheFoo'))->toBe('cacheBar');

    User::forgetModelCache('cacheKey');
    Post::forgetModelCache('cacheFoo');

    expect(User::hasModelCache('cacheKey'))->toBeFalse();
    expect(Post::hasModelCache('cacheFoo'))->toBeFalse();
});

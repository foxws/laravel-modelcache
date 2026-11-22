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
        ->toBe('modelcache-d97f1e470e4bb963ce705111f7ff6eee7a6138a8a52e52c1107409dc621975ec');

    expect($this->cacheHasher->getHashFor(app(Post::class), 'last_viewed'))
        ->toBe('modelcache-f525a9c0fb905ce6ac4bf105686c153347df32b4a81dce57b043279643995546');
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

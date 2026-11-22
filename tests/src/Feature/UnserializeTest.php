<?php

declare(strict_types=1);

use Foxws\ModelCache\Exceptions\CouldNotUnserialize;
use Foxws\ModelCache\Hasher\CacheHasher;
use Foxws\ModelCache\Serializers\DefaultSerializer;
use Foxws\ModelCache\Tests\Models\Post;
use Foxws\ModelCache\Tests\TestCase;
use Illuminate\Support\Facades\Cache;

uses(TestCase::class);

beforeEach(function () {
    config(['modelcache.cache_store' => 'array']);

    $this->post = Post::factory()->create();
});

it('follows the cache setting for the classes it unserializes', function () {
    config(['cache.serializable_classes' => false]);

    $this->post->modelCache('value', new ArrayObject([1]));

    expect($this->post->modelCached('value'))->toBeInstanceOf(__PHP_Incomplete_Class::class);
});

it('unserializes the classes its own setting allows', function () {
    config(['cache.serializable_classes' => false, 'modelcache.serializable_classes' => [ArrayObject::class]]);

    $this->post->modelCache('value', new ArrayObject([1]));

    expect($this->post->modelCached('value'))->toBeInstanceOf(ArrayObject::class);
});

it('treats a value it cannot read as a miss', function (string $stored) {
    config(['modelcache.serializable_classes' => false]);

    $this->post->modelCache('value', 'placeholder');
    Cache::store('array')->put(app(CacheHasher::class)->getHashFor($this->post, 'value'), $stored);

    expect($this->post->modelCached('value', 'default'))->toBe('default')
        ->and($this->post->modelCacheRemember('value', fn () => 'resolved'))->toBe('resolved')
        ->and($this->post->modelCached('value'))->toBe('resolved');
})->with([
    'not serialized' => 'garbage',
    'another payload' => serialize(['other' => 'payload']),
]);

it('leaves the cached value out of the exception', function () {
    expect(fn () => app(DefaultSerializer::class)->unserialize(serialize(['secret' => 'S3CRET'])))
        ->toThrow(fn (CouldNotUnserialize $exception) => expect($exception->getMessage())->not->toContain('S3CRET'));
});

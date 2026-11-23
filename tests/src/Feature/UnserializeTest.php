<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Foxws\ModelCache\Exceptions\CouldNotUnserialize;
use Foxws\ModelCache\Hasher\CacheHasher;
use Foxws\ModelCache\Serializers\DefaultSerializer;
use Foxws\ModelCache\Tests\Models\Post;
use Foxws\ModelCache\Tests\Models\Status;
use Foxws\ModelCache\Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

uses(TestCase::class);

beforeEach(function () {
    config(['modelcache.cache_store' => 'array']);

    $this->post = Post::factory()->create();
});

it('restores dates and enums by default, and no other objects', function () {
    config(['cache.serializable_classes' => null]);

    $date = CarbonImmutable::parse('2026-11-22 10:00:00.123456', 'Europe/Amsterdam');
    $this->post->modelCache('date', $date);
    $this->post->modelCache('mutable', Carbon::parse('2026-11-22 10:00:00', 'Asia/Tokyo'));
    $this->post->modelCache('enum', Status::Published);
    $this->post->modelCache('object', new ArrayObject([1]));

    expect($this->post->modelCached('date'))->toEqual($date)
        ->and($this->post->modelCached('date')->getTimezone()->getName())->toBe('Europe/Amsterdam')
        ->and($this->post->modelCached('mutable'))->toBeInstanceOf(Carbon::class)
        ->and($this->post->modelCached('enum'))->toBe(Status::Published)
        ->and($this->post->modelCached('object'))->toBeInstanceOf(__PHP_Incomplete_Class::class);
});

it('follows the cache setting when its own is null', function () {
    config(['modelcache.serializable_classes' => null, 'cache.serializable_classes' => [ArrayObject::class]]);

    $this->post->modelCache('object', new ArrayObject([1]));
    $this->post->modelCache('date', Carbon::now());

    expect($this->post->modelCached('object'))->toBeInstanceOf(ArrayObject::class)
        ->and($this->post->modelCached('date'))->toBeInstanceOf(__PHP_Incomplete_Class::class);
});

it('allows no objects when neither setting is set', function () {
    config(['modelcache.serializable_classes' => null, 'cache.serializable_classes' => null]);

    $this->post->modelCache('object', new ArrayObject([1]));

    expect($this->post->modelCached('object'))->toBeInstanceOf(__PHP_Incomplete_Class::class);
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

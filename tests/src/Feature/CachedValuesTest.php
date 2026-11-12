<?php

declare(strict_types=1);

use Foxws\ModelCache\Events\CacheMissed;
use Foxws\ModelCache\Events\ModelCacheHit;
use Foxws\ModelCache\ModelCacheRepository;
use Foxws\ModelCache\Tests\Models\Post;
use Foxws\ModelCache\Tests\TestCase;
use Illuminate\Support\Facades\Event;

uses(TestCase::class);

beforeEach(function () {
    $this->post = Post::factory()->create();
});

it('returns cached falsy values instead of the default', function (mixed $value) {
    $this->post->modelCache('value', $value);

    expect($this->post->modelCached('value', 'default'))->toBe($value);
})->with([
    'zero' => 0,
    'false' => false,
    'empty string' => '',
    'empty array' => [[]],
]);

it('caches null', function () {
    $this->post->modelCache('value', null);

    expect($this->post->modelCacheHas('value'))->toBeTrue()
        ->and($this->post->modelCached('value', 'default'))->toBeNull();
});

it('remembers a null value without resolving it again', function () {
    $calls = 0;

    $resolve = function () use (&$calls): null {
        $calls++;

        return null;
    };

    $this->post->modelCacheRemember('value', $resolve);
    $this->post->modelCacheRemember('value', $resolve);

    expect($calls)->toBe(1);
});

it('returns null for a key that is not in the cache store', function () {
    expect(app(ModelCacheRepository::class)->get('missing'))->toBeNull();
});

it('dispatches an event on a hit and on a miss', function () {
    Event::fake([ModelCacheHit::class, CacheMissed::class]);

    $this->post->modelCached('value');
    $this->post->modelCache('value', 10);
    $this->post->modelCached('value');

    Event::assertDispatchedTimes(CacheMissed::class, 1);
    Event::assertDispatched(ModelCacheHit::class, fn (ModelCacheHit $event): bool => $event->model->is($this->post) && $event->key === 'value');
});

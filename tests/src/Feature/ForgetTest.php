<?php

declare(strict_types=1);

use Foxws\ModelCache\Facades\ModelCache;
use Foxws\ModelCache\Tests\Models\Post;
use Foxws\ModelCache\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->post = Post::factory()->create();

    $this->post->modelCache('views', 10);
    $this->post->modelCache('likes', 5);
    $this->post->modelCache('shares', 1);
});

it('forgets the given keys', function () {
    ModelCache::forget($this->post, ['views', 'likes']);

    expect($this->post->modelCached('views'))->toBeNull()
        ->and($this->post->modelCached('likes'))->toBeNull()
        ->and($this->post->modelCached('shares'))->toBe(1);
});

it('forgets the keys in a collection', function () {
    ModelCache::forget($this->post, collect(['views', 'likes']));

    expect($this->post->modelCached('views'))->toBeNull()
        ->and($this->post->modelCached('likes'))->toBeNull()
        ->and($this->post->modelCached('shares'))->toBe(1);
});

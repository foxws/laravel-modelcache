<?php

declare(strict_types=1);

use Foxws\ModelCache\CacheProfiles\BaseCacheProfile;
use Foxws\ModelCache\Exceptions\InvalidModelCache;
use Foxws\ModelCache\Facades\ModelCache;
use Foxws\ModelCache\Tests\Models\Post;
use Foxws\ModelCache\Tests\Models\User;
use Foxws\ModelCache\Tests\TestCase;
use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Auth;

uses(TestCase::class);

beforeEach(function () {
    config([
        'auth.guards.other' => ['driver' => 'session', 'provider' => 'users'],
        'database.connections.tenant' => config('database.connections.sqlite'),
    ]);

    $this->post = Post::factory()->create();
});

it('shares a user\'s values between the guards they sign in through', function () {
    $user = User::factory()->create();

    Auth::guard('web')->setUser($user);
    $this->post->modelCache('position', 10);

    Auth::shouldUse('other');
    Auth::setUser($user);

    expect($this->post->modelCached('position'))->toBe(10);
});

it('keeps another kind of user with the same id apart', function () {
    $user = User::factory()->create();

    Auth::guard('web')->setUser($user);
    $this->post->modelCache('position', 10);

    $admin = (new class extends User
    {
        protected $table = 'admins';
    })->forceFill(['id' => $user->getKey()]);

    Auth::shouldUse('other');
    Auth::setUser($admin);

    expect($this->post->modelCached('position'))->toBeNull();
});

it('keeps users without a model of different guards apart', function () {
    Auth::guard('web')->setUser(new GenericUser(['id' => 1]));
    $this->post->modelCache('position', 10);

    Auth::shouldUse('other');
    Auth::setUser(new GenericUser(['id' => 1]));

    expect($this->post->modelCached('position'))->toBeNull();
});

it('keeps records of other connections and tables apart', function (Closure $change) {
    $this->post->modelCache('value', 'original');

    $other = clone $this->post;
    $change($other);

    expect($other->modelCached('value'))->toBeNull();
})->with([
    'connection' => [fn (Post $post) => $post->setConnection('tenant')],
    'table' => [fn (Post $post) => $post->setTable('archived_posts')],
]);

it('refuses an instance without a key', function (Closure $use) {
    $unsaved = new Post;

    expect(fn () => $use($unsaved))->toThrow(InvalidModelCache::class, 'has no key');
})->with([
    'write' => [fn (Post $post) => $post->modelCache('value', 1)],
    'read' => [fn (Post $post) => $post->modelCached('value')],
    'has' => [fn (Post $post) => $post->modelCacheHas('value')],
    'forget' => [fn (Post $post) => $post->modelCacheForget('value')],
]);

it('forgets the values of the namespace it is given', function () {
    $user = User::factory()->create();

    $this->post->modelCache('item12', 'guest');
    Auth::setUser($user);
    $this->post->modelCache('item1', 'user');
    Auth::forgetUser();

    ModelCache::selectCachedItems($this->post)
        ->usingSuffix(BaseCacheProfile::suffixFor($user))
        ->forKeys(['item1'])
        ->forget();

    expect($this->post->modelCached('item12'))->toBe('guest');

    Auth::setUser($user);

    expect($this->post->modelCached('item1'))->toBeNull();
});

it('uses a protected cacheNameSuffix of the model', function () {
    $post = (new class extends Post
    {
        protected $table = 'posts';

        protected function cacheNameSuffix(string $key): string
        {
            return '';
        }
    })->forceFill(['id' => $this->post->getKey()]);

    $post->modelCache('shared', 'value');
    Auth::setUser(User::factory()->create());

    expect($post->modelCached('shared'))->toBe('value');
});

it('does not write while it is disabled', function () {
    config(['modelcache.enabled' => false]);
    $this->post->modelCache('value', 'written while disabled');
    config(['modelcache.enabled' => true]);

    expect($this->post->modelCached('value'))->toBeNull();
});

it('does not construct a class that is not a model', function () {
    $class = new class
    {
        public static int $constructed = 0;

        public function __construct()
        {
            self::$constructed++;
        }
    };

    expect(fn () => ModelCache::forget($class::class, 'key'))->toThrow(InvalidModelCache::class)
        ->and($class::$constructed)->toBe(1);
});

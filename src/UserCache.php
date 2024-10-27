<?php

namespace Foxws\UserCache;

use Foxws\UserCache\CacheItemSelector\CacheItemSelector;
use Foxws\UserCache\CacheProfiles\CacheProfile;
use Foxws\UserCache\Events\ClearedUserCache;
use Foxws\UserCache\Events\ClearingUserCache;
use Foxws\UserCache\Hasher\CacheHasher;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class UserCache
{
    public function __construct(
        protected UserCacheRepository $cache,
        protected CacheHasher $hasher,
        protected CacheProfile $cacheProfile,
    ) {
        //
    }

    public function enabled(User $user): bool
    {
        return $this->cacheProfile->enabled($user);
    }

    public function shouldCache(User $user, mixed $value): bool
    {
        if (! $this->cacheProfile->shouldUseCache($user)) {
            return false;
        }

        return $this->cacheProfile->shouldCacheValue($value);
    }

    public function CacheEntry(User $user, mixed $value, ?int $lifetimeInSeconds = null): mixed
    {
        $this->cache->put(
            $this->hasher->getHashFor($user, $value),
            $value,
            $lifetimeInSeconds ?? $this->cacheProfile->cacheValueUntil($user, $value)
        );
    }

    public function hasBeenCached(User $user, mixed $value): bool
    {
        return config('usercache.enabled')
            ? $this->cache->has($this->hasher->getHashFor($user, $value))
            : false;
    }

    public function getCachedValueFor(User $user, mixed $value): mixed
    {
        return $this->cache->get($this->hasher->getHashFor($user, $value));
    }

    public function clear(array $keys = []): void
    {
        event(new ClearingUserCache);

        // $this->taggedCache($tags)->clear();

        event(new ClearedUserCache);
    }

    public function forget(string|array $keys): self
    {
        event(new ClearingUserCache);

        $keys = is_array($keys) ? $keys : func_get_args();

        $this->selectCachedItems()->forUrls($keys)->forget();

        event(new ClearedUserCache);

        return $this;
    }

    public function selectCachedItems(): CacheItemSelector
    {
        return new CacheItemSelector($this->hasher, $this->cache);
    }
}

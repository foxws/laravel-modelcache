<?php

namespace Foxws\UserCache;

use Foxws\UserCache\CacheItemSelector\CacheItemSelector;
use Foxws\UserCache\CacheProfiles\CacheProfile;
use Foxws\UserCache\Events\ClearedUserCache;
use Foxws\UserCache\Events\ClearingUserCache;
use Foxws\UserCache\Hasher\CacheHasher;

class UserCache
{
    public function __construct(
        protected UserCacheRepository $cache,
        protected CacheHasher $hasher,
        protected CacheProfile $cacheProfile,
    ) {
        //
    }

    public function enabled(): bool
    {
        return $this->cacheProfile->enabled();
    }

    public function shouldCache(string $key, mixed $value = null): bool
    {
        if (! $this->cacheProfile->shouldUseCache($key)) {
            return false;
        }

        return $this->cacheProfile->shouldCacheValue($value);
    }

    public function cache(string $key, mixed $value = null, ?int $ttl = null): mixed
    {
        $this->cache->put(
            $this->hasher->getHashFor($key),
            $value,
            $ttl ?? $this->cacheProfile->cacheValueUntil($value)
        );

        return $value;
    }

    public function hasBeenCached(string $key): bool
    {
        return config('usercache.enabled')
            ? $this->cache->has($this->hasher->getHashFor($key))
            : false;
    }

    public function getCachedValue(string $key): mixed
    {
        return $this->cache->get($this->hasher->getHashFor($key));
    }

    public function clear(array $keys = []): void
    {
        event(new ClearingUserCache);

        $this->cache->clear();

        event(new ClearedUserCache);
    }

    public function forget(string|array $keys): self
    {
        event(new ClearingUserCache);

        $keys = is_array($keys) ? $keys : func_get_args();

        $this->selectCachedItems()->forKeys($keys)->forget();

        event(new ClearedUserCache);

        return $this;
    }

    public function selectCachedItems(): CacheItemSelector
    {
        return new CacheItemSelector($this->hasher, $this->cache);
    }
}

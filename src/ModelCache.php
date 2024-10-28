<?php

namespace Foxws\ModelCache;

use ArrayAccess;
use DateTime;
use Foxws\ModelCache\CacheItemSelector\CacheItemSelector;
use Foxws\ModelCache\CacheProfiles\CacheProfile;
use Foxws\ModelCache\Events\ClearedModelCache;
use Foxws\ModelCache\Events\ClearingModelCache;
use Foxws\ModelCache\Hasher\CacheHasher;
use Illuminate\Database\Eloquent\Model;

class ModelCache
{
    public function __construct(
        protected ModelCacheRepository $cache,
        protected CacheHasher $hasher,
        protected CacheProfile $cacheProfile,
    ) {
        //
    }

    public function enabled(): bool
    {
        return $this->cacheProfile->enabled();
    }

    public function shouldCache(Model $model, string $key, mixed $value = null): bool
    {
        if (! $this->cacheProfile->shouldUseCache($model, $key)) {
            return false;
        }

        return $this->cacheProfile->shouldCacheValue($value);
    }

    public function cache(Model $model, string $key, mixed $value = null, DateTime|int|null $ttl = null): mixed
    {
        $this->cache->put(
            $this->hasher->getHashFor($model, $key),
            $value,
            $ttl ?? $this->cacheProfile->cacheValueUntil($model, $key)
        );

        return $value;
    }

    public function hasBeenCached(Model $model, string $key): bool
    {
        return config('modelcache.enabled')
            ? $this->cache->has($this->hasher->getHashFor($model, $key))
            : false;
    }

    public function getCachedValue(Model $model, string $key): mixed
    {
        return $this->cache->get($this->hasher->getHashFor($model, $key));
    }

    public function forget(Model $model, array|ArrayAccess|string $keys): self
    {
        event(new ClearingModelCache);

        $this->selectCachedItems($model)->forKeys($keys)->forget();

        event(new ClearedModelCache);

        return $this;
    }

    public function selectCachedItems(Model $model): CacheItemSelector
    {
        return (new CacheItemSelector($this->hasher, $this->cache))->forModel($model);
    }
}

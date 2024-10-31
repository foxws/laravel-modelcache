<?php

namespace Foxws\ModelCache;

use ArrayAccess;
use DateTime;
use Foxws\ModelCache\CacheItemSelector\CacheItemSelector;
use Foxws\ModelCache\CacheProfiles\CacheProfile;
use Foxws\ModelCache\Events\ClearedModelCache;
use Foxws\ModelCache\Events\ClearingModelCache;
use Foxws\ModelCache\Exceptions\InvalidModelCache;
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

    public function shouldCache(Model|string $model, string $key, mixed $value = null): bool
    {
        $model = $this->isModelCacheInstance($model);

        if (! $this->cacheProfile->shouldUseCache($model, $key)) {
            return false;
        }

        return $this->cacheProfile->shouldCacheValue($value);
    }

    public function cache(Model|string $model, string $key, mixed $value = null, DateTime|int|null $ttl = null): mixed
    {
        $model = $this->isModelCacheInstance($model);

        $ttl ??= $this->cacheProfile->cacheValueUntil($model, $key);

        $hash = $this->hasher->getHashFor($model, $key);

        $this->cache->put($hash, $value, $ttl);

        return $hash;
    }

    public function hasBeenCached(Model|string $model, string $key): bool
    {
        $model = $this->isModelCacheInstance($model);

        return config('modelcache.enabled')
            ? $this->cache->has($this->hasher->getHashFor($model, $key))
            : false;
    }

    public function getCachedValue(Model|string $model, string $key): mixed
    {
        $model = $this->isModelCacheInstance($model);

        return $this->cache->get($this->hasher->getHashFor($model, $key));
    }

    public function forget(Model|string $model, array|ArrayAccess|string $keys): self
    {
        event(new ClearingModelCache);

        $model = $this->isModelCacheInstance($model);

        $this->selectCachedItems($model)->forKeys($keys)->forget();

        event(new ClearedModelCache);

        return $this;
    }

    public function selectCachedItems(Model|string $model): CacheItemSelector
    {
        $model = $this->isModelCacheInstance($model);

        return (new CacheItemSelector($this->hasher, $this->cache))->forModel($model);
    }

    public function isModelCacheInstance(Model|string $model): Model
    {
        if (is_string($model)) {
            $model = app($model);
        }

        if ($model instanceof Model && in_array(\Foxws\ModelCache\Concerns\InteractsWithModelCache::class, class_uses_recursive($model))) {
            return $model;
        }

        throw InvalidModelCache::doesNotUseConcern((string) $model);
    }
}

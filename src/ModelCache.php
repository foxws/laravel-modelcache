<?php

declare(strict_types=1);

namespace Foxws\ModelCache;

use ArrayAccess;
use DateTimeInterface;
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
        $model = $this->resolveModel($model);

        if (! $this->enabled() || ! $this->cacheProfile->shouldUseCache($model, $key)) {
            return false;
        }

        return $this->cacheProfile->shouldCacheValue($value);
    }

    public function cache(Model|string $model, string $key, mixed $value = null, DateTimeInterface|int|null $ttl = null): mixed
    {
        $model = $this->resolveModel($model);

        $ttl ??= $this->cacheProfile->cacheValueUntil($model, $key);

        $hash = $this->hasher->getHashFor($model, $key);

        $this->cache->put($hash, $value, $ttl);

        return $hash;
    }

    public function hasBeenCached(Model|string $model, string $key): bool
    {
        $model = $this->resolveModel($model);

        return $this->enabled()
            && $this->cache->has($this->hasher->getHashFor($model, $key));
    }

    public function getCachedValue(Model|string $model, string $key, mixed $default = null): mixed
    {
        $model = $this->resolveModel($model);

        return $this->cache->get($this->hasher->getHashFor($model, $key), $default);
    }

    /**
     * @param  array<int, string>|(ArrayAccess<int, string>&\Traversable<int, string>)|string  $keys  One key, or several, e.g. an array or a collection.
     */
    public function forget(Model|string $model, array|ArrayAccess|string $keys): self
    {
        $items = $this->selectCachedItems($model);

        event(new ClearingModelCache);

        $items->forKeys($keys)->forget();

        event(new ClearedModelCache);

        return $this;
    }

    public function selectCachedItems(Model|string $model): CacheItemSelector
    {
        $model = $this->resolveModel($model);

        return (new CacheItemSelector($this->hasher, $this->cache))->forModel($model);
    }

    public function isModelCacheInstance(Model|string $model): Model
    {
        if ($model instanceof Model) {
            return $model;
        }

        // Checked first, as resolving runs the constructor of whatever class it's given.
        if (! is_a($model, Model::class, true)) {
            throw InvalidModelCache::doesNotExtendModel($model);
        }

        return app($model);
    }

    /**
     * The model to cache for: a class stands for all of its records, and an instance for its own one.
     * Without a key, e.g. unsaved or selected without it, an instance would get the cache of the class.
     *
     * @throws InvalidModelCache
     */
    protected function resolveModel(Model|string $model): Model
    {
        if ($model instanceof Model && in_array($model->getKey(), [null, ''], true)) {
            throw InvalidModelCache::hasNoKey($model);
        }

        return $this->isModelCacheInstance($model);
    }
}

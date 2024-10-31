<?php

namespace Foxws\ModelCache\Concerns;

use DateTime;
use Foxws\ModelCache\Facades\ModelCache;

trait InteractsWithModelCache
{
    public static function modelClassCache(string $key, mixed $value = null, DateTime|int|null $ttl = null): mixed
    {
        if (! ModelCache::shouldCache(static::class, $key, $value)) {
            return null;
        }

        return ModelCache::cache(static::class, $key, $value, $ttl);
    }

    public static function modelClassCached(string $key, mixed $default = null): mixed
    {
        if (! ModelCache::enabled() || ! static::isModelClassCached($key)) {
            return $default;
        }

        return ModelCache::getCachedValue(static::class, $key) ?? $default;
    }

    public static function modelClassCacheForget(string $key): void
    {
        ModelCache::forget(static::class, $key);
    }

    public static function isModelClassCached(string $key): bool
    {
        return ModelCache::hasBeenCached(static::class, $key);
    }

    public function modelCache(string $key, mixed $value = null, DateTime|int|null $ttl = null): mixed
    {
        if (! ModelCache::shouldCache($this, $key, $value)) {
            return null;
        }

        return ModelCache::cache($this, $key, $value, $ttl);
    }

    public function modelCached(string $key, mixed $default = null): mixed
    {
        if (! ModelCache::enabled() || ! $this->isModelCached($key)) {
            return $default;
        }

        return ModelCache::getCachedValue($this, $key) ?? $default;
    }

    public function isModelCached(string $key): bool
    {
        return ModelCache::hasBeenCached($this, $key);
    }

    public function modelCacheForget(string $key): void
    {
        ModelCache::forget($this, $key);
    }
}

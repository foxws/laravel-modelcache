<?php

namespace Foxws\ModelCache\Concerns;

use DateTime;
use Foxws\ModelCache\Facades\ModelCache;
use Illuminate\Database\Eloquent\Model;

trait InteractsWithModelCache
{
    public static function setModelCache(string $key, mixed $value = null, DateTime|int|null $ttl = null, ?Model $model = null): mixed
    {
        $instance = $model ?? static::class;

        if (! ModelCache::shouldCache($instance, $key, $value)) {
            return null;
        }

        return ModelCache::cache($instance, $key, $value, $ttl);
    }

    public static function getModelCache(string $key, mixed $default = null, ?Model $model = null): mixed
    {
        $instance = $model ?? static::class;

        if (! ModelCache::enabled() || ! ModelCache::hasBeenCached($instance, $key)) {
            return $default;
        }

        return ModelCache::getCachedValue($instance, $key) ?: $default;
    }

    public static function forgetModelCache(string $key, ?Model $model = null): ModelCache
    {
        $instance = $model ?? static::class;

        return ModelCache::forget($instance, $key);
    }

    public static function isModelCached(string $key, ?Model $model = null): bool
    {
        $instance = $model ?? static::class;

        return ModelCache::hasBeenCached($instance, $key);
    }

    public function modelCache(string $key, mixed $value = null, DateTime|int|null $ttl = null): mixed
    {
        return static::setModelCache(model: $this, key: $key, value: $value, ttl: $ttl);
    }

    public function modelCached(string $key, mixed $default = null): mixed
    {
        return static::getModelCache(model: $this, key: $key, default: $default);
    }

    public function forgetModelCached(string $key): ModelCache
    {
        return static::forgetModelCache(model: $this, key: $key);
    }
}

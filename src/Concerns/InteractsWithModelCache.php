<?php

declare(strict_types=1);

namespace Foxws\ModelCache\Concerns;

use DateTimeInterface;
use Foxws\ModelCache\Facades\ModelCache;
use Illuminate\Database\Eloquent\Model;

trait InteractsWithModelCache
{
    public static function setModelCache(string $key, mixed $value = null, DateTimeInterface|int|null $ttl = null, ?Model $model = null): mixed
    {
        $instance = $model ?? static::class;

        if ($instance instanceof static && ! $instance->shouldModelCache($key, $value)) {
            return null;
        }

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

    public static function forgetModelCache(string $key, ?Model $model = null): mixed
    {
        $instance = $model ?? static::class;

        return ModelCache::forget($instance, $key);
    }

    public static function hasModelCache(string $key, ?Model $model = null): bool
    {
        $instance = $model ?? static::class;

        return ModelCache::hasBeenCached($instance, $key);
    }

    public function modelCache(string $key, mixed $value = null, DateTimeInterface|int|null $ttl = null): mixed
    {
        return static::setModelCache(model: $this, key: $key, value: $value, ttl: $ttl);
    }

    public function modelCached(string $key, mixed $default = null): mixed
    {
        return static::getModelCache(model: $this, key: $key, default: $default);
    }

    public function modelCacheForget(string $key): mixed
    {
        return static::forgetModelCache(model: $this, key: $key);
    }

    public function modelCacheHas(string $key): mixed
    {
        return static::hasModelCache(model: $this, key: $key);
    }

    public function modelCacheRemember(string $key, mixed $value, DateTimeInterface|int|null $ttl = null): mixed
    {
        if ($this->modelCacheHas($key)) {
            return $this->modelCached($key);
        }

        $resolved = value($value);

        $this->modelCache($key, $resolved, $ttl);

        return $resolved;
    }

    public function shouldModelCache(string $key, mixed $value = null): bool
    {
        return true;
    }
}

<?php

declare(strict_types=1);

namespace Foxws\ModelCache;

use DateTimeInterface;
use Foxws\ModelCache\Serializers\Serializer;
use Illuminate\Cache\Repository;

class ModelCacheRepository
{
    public function __construct(
        protected Serializer $serializer,
        protected Repository $cache,
    ) {
        //
    }

    public function put(string $key, mixed $value = null, DateTimeInterface|int|null $seconds = null): void
    {
        $this->cache->put($key, $this->serializer->serialize($value), is_numeric($seconds) ? now()->addSeconds($seconds) : $seconds);
    }

    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }

    /**
     * The cached value, or the default when the key is not in the store (e.g. it expired after has() was checked).
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $serializedValue = $this->cache->get($key);

        if (! is_string($serializedValue)) {
            return $default;
        }

        return $this->serializer->unserialize($serializedValue);
    }

    public function clear(): void
    {
        $this->cache->clear();
    }

    public function forget(string $key): bool
    {
        return $this->cache->forget($key);
    }
}

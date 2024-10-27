<?php

namespace Foxws\UserCache;

use Foxws\UserCache\Serializers\Serializer;
use Illuminate\Cache\Repository;
use Symfony\Component\HttpFoundation\Response;

class UserCacheRepository
{
    public function __construct(
        protected Serializer $serializer,
        protected Repository $cache,
    ) {
        //
    }

    public function put(string $key, mixed $value = null, \DateTime|int $seconds): void
    {
        $this->cache->put($key, $this->serializer->serialize($value), is_numeric($seconds) ? now()->addSeconds($seconds) : $seconds);
    }

    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }

    public function get(string $key): Response
    {
        return $this->serializer->unserialize($this->cache->get($key) ?? '');
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

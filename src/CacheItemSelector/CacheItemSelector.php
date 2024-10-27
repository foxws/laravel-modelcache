<?php

namespace Foxws\UserCache\CacheItemSelector;

use Foxws\UserCache\Hasher\CacheHasher;
use Foxws\UserCache\ResponseCacheRepository;
use Foxws\UserCache\UserCacheRepository;
use Illuminate\Foundation\Auth\User;

class CacheItemSelector extends AbstractCacheBuilder
{
    protected array $keys;

    public function __construct(
        protected CacheHasher $hasher,
        protected UserCacheRepository $cache,
    ) {}

    public function forKeys(string | array $keys): static
    {
        $this->keys = is_array($keys) ? $keys : func_get_args();

        return $this;
    }

    public function forget(): void
    {
        collect($this->keys)
            ->map(function ($key) {
                $value = $this->build($key);

                return $this->hasher->getHashFor($value);
            })
            ->filter(fn($hash) => $this->cache->has($hash))
            ->each(fn($hash) => $this->cache->forget($hash));
    }
}

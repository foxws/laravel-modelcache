<?php

namespace Foxws\ModelCache\CacheItemSelector;

use ArrayAccess;
use Foxws\ModelCache\Hasher\CacheHasher;
use Foxws\ModelCache\ModelCacheRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CacheItemSelector extends AbstractCacheBuilder
{
    protected ?Model $model = null;

    protected ?array $keys = null;

    public function __construct(
        protected CacheHasher $hasher,
        protected ModelCacheRepository $cache,
    ) {}

    public function forModel(?Model $model = null): static
    {
        $this->model = $model;

        return $this;
    }

    public function forKeys(ArrayAccess|array|string|null $keys = null): static
    {
        $this->keys = Arr::wrap($keys);

        return $this;
    }

    public function forget(): void
    {
        collect($this->keys)
            ->map(function ($key) {
                $key = $this->build($key);

                return $this->hasher->getHashFor($this->model, $key);
            })
            ->filter(fn($hash) => $this->cache->has($hash))
            ->each(fn($hash) => $this->cache->forget($hash));
    }
}

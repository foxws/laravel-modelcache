<?php

declare(strict_types=1);

namespace Foxws\ModelCache\CacheItemSelector;

use ArrayAccess;
use Foxws\ModelCache\Hasher\CacheHasher;
use Foxws\ModelCache\ModelCacheRepository;
use Illuminate\Database\Eloquent\Model;
use LogicException;
use Traversable;

class CacheItemSelector extends AbstractCacheBuilder
{
    protected ?Model $model = null;

    /** @var array<int, string> */
    protected array $keys = [];

    public function __construct(
        protected CacheHasher $hasher,
        protected ModelCacheRepository $cache,
    ) {}

    public function forModel(?Model $model = null): static
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param  array<int, string>|(ArrayAccess<int, string>&Traversable<int, string>)|string|null  $keys  One key, or several, e.g. an array or a collection.
     */
    public function forKeys(ArrayAccess|array|string|null $keys = null): static
    {
        $this->keys = match (true) {
            is_array($keys) => array_values($keys),
            $keys instanceof Traversable => iterator_to_array($keys, false),
            is_string($keys) => [$keys],
            default => [],
        };

        return $this;
    }

    public function forget(): void
    {
        $model = $this->model ?? throw new LogicException('Call forModel() before forget().');

        collect($this->keys)
            ->map(fn (string $key): string => $this->hasher->getHashFor($model, $this->build($key)))
            ->filter(fn ($hash) => $this->cache->has($hash))
            ->each(fn ($hash) => $this->cache->forget($hash));
    }
}

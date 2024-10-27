<?php

namespace Foxws\UserCache;

use Foxws\UserCache\CacheItemSelector\CacheItemSelector;
use Foxws\UserCache\CacheProfiles\CacheProfile;
use Foxws\UserCache\Hasher\EloquentHasher;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class UserCache
{
    public function __construct(
        protected UserCacheRepository $cache,
        protected EloquentHasher $hasher,
        protected CacheProfile $cacheProfile,
    ) {
        //
    }

    public function enabled(User $user): bool
    {
        return $this->cacheProfile->enabled($user);
    }

    public function shouldCache(User $user, mixed $data): bool
    {
        if (! $this->cacheProfile->shouldUseCache($user)) {
            return false;
        }

        return $this->cacheProfile->shouldCacheData($data);
    }

    public function cacheData(
        User $user,
        mixed $data,
        ?int $lifetimeInSeconds = null,
    ): mixed {
        if (config('usercache.add_cache_time_key')) {
            $value = $this->addCachedKey($response);
        }

        return $value;
    }

    public function hasBeenCached(mixed $data): bool
    {
        return config('usercache.enabled')
            ? $this->cache->has($this->hasher->getHashFor($data))
            : false;
    }

    public function getCachedDataFor(string $key): mixed
    {
        return $this->taggedCache($tags)->get($this->hasher->getHashFor($request));
    }

    public function clear(array $tags = []): void
    {
        event(new ClearingUserCache);

        $this->taggedCache($tags)->clear();

        event(new ClearedUserCache);
    }

    protected function addCachedHeader(Response $response): Response
    {
        $clonedResponse = clone $response;

        $clonedResponse->headers->set(
            config('UserCache.cache_time_header_name'),
            Carbon::now()->toRfc2822String(),
        );

        return $clonedResponse;
    }

    /**
     * @param  string[]  $tags
     * @return \Spatie\UserCache\UserCache
     */
    public function forget(string|array $uris, array $tags = []): self
    {
        event(new ClearingUserCache);

        $uris = is_array($uris) ? $uris : func_get_args();
        $this->selectCachedItems()->forUrls($uris)->forget();

        event(new ClearedUserCache);

        return $this;
    }

    public function selectCachedItems(): CacheItemSelector
    {
        return new CacheItemSelector($this->hasher, $this->cache);
    }
}

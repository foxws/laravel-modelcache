<?php

namespace Foxws\UserCache;

use Foxws\UserCache\CacheItemSelector\CacheItemSelector;
use Foxws\UserCache\CacheProfiles\CacheProfile;
use Foxws\UserCache\Hasher\EloquentHasher;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
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

    public function shouldCache(User $user, Response $response): bool
    {
        if (! $this->cacheProfile->shouldUseCache($request)) {
            return false;
        }

        return $this->cacheProfile->shouldCacheResponse($response);
    }

    public function shouldBypass(Request $request): bool
    {
        // Ensure we return if cache_bypass_header is not setup
        if (! config('UserCache.cache_bypass_header.name')) {
            return false;
        }
        // Ensure we return if cache_bypass_header is not setup
        if (! config('UserCache.cache_bypass_header.value')) {
            return false;
        }

        return $request->header(config('UserCache.cache_bypass_header.name')) === (string) config('UserCache.cache_bypass_header.value');
    }

    public function cacheResponse(
        Request $request,
        Response $response,
        ?int $lifetimeInSeconds = null,
        array $tags = []
    ): Response {
        if (config('UserCache.add_cache_time_header')) {
            $response = $this->addCachedHeader($response);
        }

        $this->taggedCache($tags)->put(
            $this->hasher->getHashFor($request),
            $response,
            $lifetimeInSeconds ?? $this->cacheProfile->cacheRequestUntil($request),
        );

        return $response;
    }

    public function hasBeenCached(Request $request, array $tags = []): bool
    {
        return config('UserCache.enabled')
            ? $this->taggedCache($tags)->has($this->hasher->getHashFor($request))
            : false;
    }

    public function getCachedResponseFor(Request $request, array $tags = []): Response
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

    protected function taggedCache(array $tags = []): UserCacheRepository
    {
        if (empty($tags)) {
            return $this->cache;
        }

        return $this->cache->tags($tags);
    }
}

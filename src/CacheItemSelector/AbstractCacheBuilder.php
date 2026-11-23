<?php

declare(strict_types=1);

namespace Foxws\ModelCache\CacheItemSelector;

abstract class AbstractCacheBuilder
{
    protected ?string $cacheNameSuffix = null;

    /**
     * Select the items in this namespace instead of the current one, e.g. BaseCacheProfile::suffixFor($user)
     * for a user's, or '' for the guests'.
     */
    public function usingSuffix(?string $cacheNameSuffix = null): static
    {
        $this->cacheNameSuffix = $cacheNameSuffix;

        return $this;
    }
}

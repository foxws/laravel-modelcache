<?php

namespace Foxws\UserCache\CacheItemSelector;

abstract class AbstractCacheBuilder
{
    protected ?string $cacheNameSuffix = null;

    public function usingSuffix($cacheNameSuffix): self
    {
        $this->cacheNameSuffix = $cacheNameSuffix;

        return $this;
    }

    protected function build(string $key): string
    {
        if (isset($this->cacheNameSuffix)) {
            $key .= $this->cacheNameSuffix;
        }

        return $key;
    }
}

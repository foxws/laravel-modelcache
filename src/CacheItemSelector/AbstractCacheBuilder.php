<?php

namespace Foxws\ModelCache\CacheItemSelector;

abstract class AbstractCacheBuilder
{
    protected ?string $cacheNameSuffix = null;

    public function usingSuffix(?string $cacheNameSuffix = null): self
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

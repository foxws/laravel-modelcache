<?php

namespace Foxws\ModelCache\CacheItemSelector;

use Foxws\ModelCache\Tests\Models\User;

abstract class AbstractCacheBuilder
{
    protected ?string $cacheNameSuffix = null;

    public function usingSuffix(?string $cacheNameSuffix = null): self
    {
        $this->cacheNameSuffix = $cacheNameSuffix;

        return $this;
    }

    protected function build(User $user, string $key): string
    {
        if (isset($this->cacheNameSuffix)) {
            $key .= $this->cacheNameSuffix;
        }

        return $key;
    }
}

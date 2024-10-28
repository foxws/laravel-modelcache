<?php

namespace Foxws\ModelCache\CacheItemSelector;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractCacheBuilder
{
    protected ?string $cacheNameSuffix = null;

    public function usingSuffix(?string $cacheNameSuffix = null): self
    {
        $this->cacheNameSuffix = $cacheNameSuffix;

        return $this;
    }

    protected function build(Model $model, string $key): string
    {
        if (isset($this->cacheNameSuffix)) {
            $key .= $this->cacheNameSuffix;
        }

        return $key;
    }
}

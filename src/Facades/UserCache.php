<?php

namespace Foxws\ModelCache\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Foxws\ModelCache\ModelCache
 */
class ModelCache extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Foxws\ModelCache\ModelCache::class;
    }
}

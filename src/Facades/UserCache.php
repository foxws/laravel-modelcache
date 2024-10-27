<?php

namespace Foxws\UserCache\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Foxws\UserCache\UserCache
 */
class UserCache extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Foxws\UserCache\UserCache::class;
    }
}

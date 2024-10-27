<?php

namespace Spatie\MediaLibrary\MediaCollections\Models\Concerns;

use Foxws\UserCache\Facades\UserCache;
use Illuminate\Database\Eloquent\Model;

trait InteractsWithUserCache
{
    public static function cacheEntry(mixed $value)
    {
        //
    }
}

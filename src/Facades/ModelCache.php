<?php

declare(strict_types=1);

namespace Foxws\ModelCache\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Foxws\ModelCache\ModelCache
 *
 * @method static bool enabled()
 * @method static bool shouldCache(\Illuminate\Database\Eloquent\Model|string $model, string $key, mixed $value = null)
 * @method static mixed cache(\Illuminate\Database\Eloquent\Model|string $model, string $key, mixed $value = null, \DateTime|int|null $ttl = null)
 * @method static bool hasBeenCached(\Illuminate\Database\Eloquent\Model|string $model, string $key)
 * @method static mixed getCachedValue(\Illuminate\Database\Eloquent\Model|string $model, string $key)
 * @method static \Foxws\ModelCache\ModelCache forget(\Illuminate\Database\Eloquent\Model|string $model, array|\ArrayAccess|string $keys)
 */
class ModelCache extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'modelcache';
    }
}

<?php

declare(strict_types=1);

namespace Foxws\ModelCache\Hasher;

use Illuminate\Database\Eloquent\Model;

interface CacheHasher
{
    /**
     * @param  string|null  $suffix  The namespace to use instead of the current one, e.g. another user's.
     */
    public function getHashFor(Model $model, string $key, ?string $suffix = null): string;
}

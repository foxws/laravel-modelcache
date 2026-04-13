<?php

declare(strict_types=1);

namespace Foxws\ModelCache\Hasher;

use Illuminate\Database\Eloquent\Model;

interface CacheHasher
{
    public function getHashFor(Model $model, string $key): string;
}

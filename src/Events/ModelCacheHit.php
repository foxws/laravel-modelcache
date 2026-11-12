<?php

declare(strict_types=1);

namespace Foxws\ModelCache\Events;

use Illuminate\Database\Eloquent\Model;

class ModelCacheHit
{
    public function __construct(
        public Model $model,
        public string $key,
    ) {
        //
    }
}

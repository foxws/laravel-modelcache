<?php

declare(strict_types=1);

namespace Foxws\ModelCache\Events;

use Illuminate\Database\Eloquent\Model;

class CacheMissed
{
    public function __construct(
        public Model $model,
        public string $key,
    ) {
        //
    }
}

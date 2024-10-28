<?php

namespace Foxws\ModelCache\Events;

class ModelCacheHit
{
    public function __construct(
        public string $key,
    ) {
        //
    }
}

<?php

namespace Foxws\ModelCache\Events;

class CacheMissed
{
    public function __construct(
        public string $key,
    ) {
        //
    }
}

<?php

namespace Foxws\UserCache\Events;

class CacheMissed
{
    public function __construct(
        public mixed $value,
    ) {
        //
    }
}

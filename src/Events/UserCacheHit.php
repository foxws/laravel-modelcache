<?php

namespace Foxws\UserCache\Events;

class UserCacheHit
{
    public function __construct(
        public mixed $value,
    ) {
        //
    }
}

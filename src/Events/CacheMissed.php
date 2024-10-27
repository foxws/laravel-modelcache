<?php

namespace Foxws\UserCache\Events;

use Illuminate\Foundation\Auth\User;

class CacheMissed
{
    public function __construct(
        public User $user,
    ) {
        //
    }
}

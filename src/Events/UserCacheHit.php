<?php

namespace Foxws\UserCache\Events;

use Illuminate\Foundation\Auth\User;

class UserCacheHit
{
    public function __construct(
        public User $user,
    ) {
        //
    }
}

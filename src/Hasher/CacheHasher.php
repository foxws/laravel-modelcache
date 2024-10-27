<?php

namespace Foxws\UserCache\Hasher;

use Illuminate\Foundation\Auth\User;

interface CacheHasher
{
    public function getHashFor(User $user, mixed $value): string;
}

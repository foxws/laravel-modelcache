<?php

namespace Foxws\UserCache\Hasher;

use Illuminate\Foundation\Auth\User;

interface EloquentHasher
{
    public function getHashFor(User $user): string;
}

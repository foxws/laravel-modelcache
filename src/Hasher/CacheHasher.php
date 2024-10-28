<?php

namespace Foxws\ModelCache\Hasher;

use Illuminate\Foundation\Auth\User;

interface CacheHasher
{
    public function getHashFor(User $user, string $key): string;
}

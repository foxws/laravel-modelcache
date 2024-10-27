<?php

namespace Foxws\UserCache\Hasher;

interface CacheHasher
{
    public function getHashFor(string $key, mixed $value): string;
}

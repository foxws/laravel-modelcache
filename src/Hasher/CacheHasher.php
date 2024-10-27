<?php

namespace Foxws\UserCache\Hasher;

interface CacheHasher
{
    public function getHashFor(mixed $value): string;
}

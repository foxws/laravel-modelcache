<?php

namespace Foxws\UserCache\Exceptions;

use Exception;

class CouldNotUnserialize extends Exception
{
    public static function serializedCacheValue(string $serializeValue): static
    {
        return new static("Could not unserialize serialized value `{$serializeValue}`");
    }
}

<?php

namespace Foxws\UserCache\Exceptions;

use Exception;

class CouldNotUnserialize extends Exception
{
    public static function serializedCacheData(string $serializedData): static
    {
        return new static("Could not unserialize serialized data `{$serializedData}`");
    }
}

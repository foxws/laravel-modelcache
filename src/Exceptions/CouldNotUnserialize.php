<?php

declare(strict_types=1);

namespace Foxws\ModelCache\Exceptions;

use Exception;
use Throwable;

class CouldNotUnserialize extends Exception
{
    /**
     * The value is left out of the message, as it ends up in logs and can be private.
     */
    public static function serializedCacheValue(?Throwable $previous = null): static
    {
        return new static('Could not unserialize the cached value.', previous: $previous);
    }
}

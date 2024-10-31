<?php

namespace Foxws\ModelCache\Exceptions;

use Exception;

class InvalidModelCache extends Exception
{
    public static function doesNotUseConcern(string $modelClass): static
    {
        return new static("The provided `{$modelClass}` does not use the ModelCache trait.");
    }
}

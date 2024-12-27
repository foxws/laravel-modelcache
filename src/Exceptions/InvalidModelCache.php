<?php

namespace Foxws\ModelCache\Exceptions;

use Exception;

class InvalidModelCache extends Exception
{
    public static function doesNotExtendModel(string $modelClass): static
    {
        return new static("The provided `{$modelClass}` does not extend `Illuminate\Database\Eloquent\Model`.");
    }
}

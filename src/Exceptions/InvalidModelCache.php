<?php

declare(strict_types=1);

namespace Foxws\ModelCache\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class InvalidModelCache extends Exception
{
    public static function hasNoKey(Model $model): static
    {
        $class = $model::class;

        return new static("The `{$class}` instance has no key, so it can't have a cache of its own. Save it or select its key, or use the static methods for a value of the whole class.");
    }

    public static function doesNotExtendModel(string $modelClass): static
    {
        return new static("The provided `{$modelClass}` does not extend `Illuminate\Database\Eloquent\Model`.");
    }
}

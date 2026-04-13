<?php

declare(strict_types=1);

namespace Foxws\ModelCache\Serializers;

interface Serializer
{
    public function serialize(mixed $value = null): string;

    public function unserialize(string $serializedValue): mixed;
}

<?php

namespace Foxws\ModelCache\Serializers;

interface Serializer
{
    public function serialize(mixed $value = null): string;

    public function unserialize(string $serializedValue): mixed;
}

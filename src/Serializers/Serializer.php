<?php

namespace Foxws\UserCache\Serializers;

interface Serializer
{
    public function serialize(mixed $value): string;

    public function unserialize(string $serializedValue): mixed;
}

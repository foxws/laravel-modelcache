<?php

namespace Foxws\UserCache\Serializers;

interface Serializer
{
    public function serialize(mixed $data): string;

    public function unserialize(string $serializedData): mixed;
}

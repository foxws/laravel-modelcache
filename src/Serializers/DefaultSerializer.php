<?php

namespace Foxws\USerCache\Serializers;

use Foxws\UserCache\Exceptions\CouldNotUnserialize;

class DefaultSerializer implements Serializer
{
    public const CACHE_TYPE_NORMAL = 'normal';

    public function serialize(mixed $value): string
    {
        return serialize($this->getCacheValue($value));
    }

    public function unserialize(string $serializedValue): mixed
    {
        $valueProperties = unserialize($serializedValue);

        if (! $this->containsValidCacheProperties($valueProperties)) {
            throw CouldNotUnserialize::serializedCacheData($serializedValue);
        }

        return $this->buildCacheValue($valueProperties);
    }

    protected function getCacheValue(mixed $value): array
    {
        return compact('data', 'type');
    }

    protected function containsValidCacheProperties(mixed $properties): bool
    {
        if (! is_array($properties)) {
            return false;
        }

        return isset($properties['data']);
    }

    protected function buildCacheValue(array $dataProperties): mixed
    {
        // $type = $dataProperties['type'] ?? static::CACHE_TYPE_NORMAL;

        return $dataProperties['data'];
    }
}

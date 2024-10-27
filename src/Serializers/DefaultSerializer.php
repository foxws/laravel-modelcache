<?php

namespace Foxws\UserCache\Serializers;

use Foxws\UserCache\Exceptions\CouldNotUnserialize;

class DefaultSerializer implements Serializer
{
    public const CACHE_TYPE_NORMAL = 'normal';

    public function serialize(mixed $value): string
    {
        return serialize($this->getCacheData($value));
    }

    public function unserialize(string $serializedValue): mixed
    {
        $cacheProperties = unserialize($serializedValue);

        if (! $this->containsValidCacheProperties($cacheProperties)) {
            throw CouldNotUnserialize::serializedCacheValue($serializedValue);
        }

        return $this->buildCacheValue($cacheProperties);
    }

    protected function getCacheData(mixed $value): array
    {
        $type = static::CACHE_TYPE_NORMAL;

        return compact('value', 'type');
    }

    protected function containsValidCacheProperties(mixed $properties): bool
    {
        if (! is_array($properties)) {
            return false;
        }

        return isset($properties['value']);
    }

    protected function buildCacheValue(array $cacheProperties): mixed
    {
        $type = $cacheProperties['type'] ?? static::CACHE_TYPE_NORMAL;

        return $cacheProperties['value'];
    }
}

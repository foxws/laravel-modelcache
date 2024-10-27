<?php

namespace Foxws\USerCache\Serializers;

use Foxws\UserCache\Exceptions\CouldNotUnserialize;

class DefaultSerializer implements Serializer
{
    public const CACHE_TYPE_NORMAL = 'normal';

    public function serialize(mixed $data): string
    {
        return serialize($this->getCacheData($data));
    }

    public function unserialize(string $serializedData): mixed
    {
        $dataProperties = unserialize($serializedData);

        if (! $this->containsValidCacheProperties($dataProperties)) {
            throw CouldNotUnserialize::serializedCacheData($serializedData);
        }

        return $this->buildCacheData($dataProperties);
    }

    protected function getCacheData(mixed $data): array
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

    protected function buildCacheData(array $dataProperties): mixed
    {
        // $type = $dataProperties['type'] ?? static::CACHE_TYPE_NORMAL;

        return $dataProperties['data'];
    }
}

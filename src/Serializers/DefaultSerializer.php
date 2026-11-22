<?php

declare(strict_types=1);

namespace Foxws\ModelCache\Serializers;

use Foxws\ModelCache\Exceptions\CouldNotUnserialize;
use Illuminate\Support\Facades\Config;
use Throwable;

class DefaultSerializer implements Serializer
{
    public const CACHE_TYPE_NORMAL = 'normal';

    public function serialize(mixed $value = null): string
    {
        return serialize($this->getCacheData($value));
    }

    public function unserialize(string $serializedValue): mixed
    {
        try {
            // Suppressed, as Laravel turns the warning about an unreadable value into an exception.
            $cacheProperties = @unserialize($serializedValue, ['allowed_classes' => $this->allowedClasses()]);
        } catch (Throwable $exception) {
            throw CouldNotUnserialize::serializedCacheValue($exception);
        }

        if (! $this->containsValidCacheProperties($cacheProperties)) {
            throw CouldNotUnserialize::serializedCacheValue();
        }

        return $this->buildCacheValue($cacheProperties);
    }

    /**
     * @return array{value: mixed, type: string}
     */
    protected function getCacheData(mixed $value = null): array
    {
        $type = static::CACHE_TYPE_NORMAL;

        return compact('value', 'type');
    }

    /**
     * @return array<int, class-string>|bool
     */
    protected function allowedClasses(): array|bool
    {
        $classes = Config::get('modelcache.serializable_classes') ?? Config::get('cache.serializable_classes') ?? true;

        return is_array($classes) || is_bool($classes) ? $classes : false;
    }

    protected function containsValidCacheProperties(mixed $properties): bool
    {
        if (! is_array($properties)) {
            return false;
        }

        return array_key_exists('value', $properties);
    }

    /**
     * @param  array<string, mixed>  $cacheProperties
     */
    protected function buildCacheValue(array $cacheProperties): mixed
    {
        return $cacheProperties['value'];
    }
}

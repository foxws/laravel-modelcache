<?php

declare(strict_types=1);

namespace Foxws\ModelCache\Hasher;

use Foxws\ModelCache\CacheProfiles\CacheProfile;
use Illuminate\Database\Eloquent\Model;
use ReflectionMethod;

class DefaultHasher implements CacheHasher
{
    public function __construct(
        protected CacheProfile $cacheProfile,
    ) {
        //
    }

    public function getHashFor(Model $model, string $key, ?string $suffix = null): string
    {
        $cacheNameSuffix = $suffix ?? $this->getCacheNameSuffix($model, $key);

        $payload = json_encode([
            $this->getNormalizedModel($model),
            $key,
            $cacheNameSuffix,
        ], JSON_THROW_ON_ERROR);

        // Not a fast hash: keys can hold user input, which mustn't be able to collide with other keys.
        return 'modelcache-'.hash('sha256', $payload);
    }

    /**
     * Records of different connections, databases or tables can have the same class and key.
     *
     * @return array<int, string>
     */
    protected function getNormalizedModel(Model $model): array
    {
        $connection = $model->getConnection();

        return [
            (string) $connection->getName(),
            $connection->getDatabaseName(),
            $connection->getTablePrefix(),
            $model->getTable(),
            $model->getMorphClass(),
            (string) $model->getKey(),
        ];
    }

    protected function getCacheNameSuffix(Model $model, string $key): string
    {
        if (method_exists($model, 'cacheNameSuffix')) {
            // Through reflection, as models declare it protected and would forward the call to a query.
            return (string) new ReflectionMethod($model, 'cacheNameSuffix')->invoke($model, $key);
        }

        return $this->cacheProfile->useCacheNameSuffix($model, $key);
    }
}

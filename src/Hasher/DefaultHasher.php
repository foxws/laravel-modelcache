<?php

namespace Foxws\ModelCache\Hasher;

use Foxws\ModelCache\CacheProfiles\CacheProfile;
use Illuminate\Database\Eloquent\Model;

class DefaultHasher implements CacheHasher
{
    public function __construct(
        protected CacheProfile $cacheProfile,
    ) {
        //
    }

    public function getHashFor(Model $model, string $key): string
    {
        $cacheNameSuffix = $this->getCacheNameSuffix($model, $key);

        return 'modelcache-' . hash(
            'xxh128',
            implode(':', [$this->getNormalizedModel($model), $key, $cacheNameSuffix])
        );
    }

    protected function getNormalizedModel(Model $model): string
    {
        return "{$model->getMorphClass()}:{$model->getKey()}";
    }

    protected function getCacheNameSuffix(Model $model, string $key): string
    {
        if (method_exists($model, 'cacheNameSuffix')) {
            return $model->cacheNameSuffix($key);
        }

        return $this->cacheProfile->useCacheNameSuffix($model, $key);
    }
}

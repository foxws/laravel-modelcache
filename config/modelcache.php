<?php

declare(strict_types=1);

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Foxws\ModelCache\CacheProfiles\CacheAllSuccessful;
use Foxws\ModelCache\Hasher\DefaultHasher;
use Foxws\ModelCache\Serializers\DefaultSerializer;
use Illuminate\Support\Carbon as SupportCarbon;

return [
    /*
     * Determine if the model cache should be enabled.
     */
    'enabled' => (bool) env('MODEL_CACHE_ENABLED', true),

    /*
     * The given class will determinate if a value should be cached.
     * You can provide your own classes given that it implements the
     * CacheProfile interface.
     */
    'cache_profile' => CacheAllSuccessful::class,

    /*
     * This must be the name of any store that is configured in config/cache.php.
     */
    'cache_store' => env('MODEL_CACHE_STORE') ?: env('CACHE_STORE', 'database'),

    /*
     * Default time-to-live for cache items in seconds.
     */
    'cache_lifetime_in_seconds' => (int) env('MODEL_CACHE_LIFETIME', 60 * 60 * 24 * 7),

    /*
     * This class is responsible for generating a hash for a request. This hash
     * is used to look up a cached response.
     */
    'hasher' => DefaultHasher::class,

    /*
     * The classes cached values may be unserialized into: an array of class
     * names, false for none, or true for any, which lets whoever can write to
     * the cache store create any object. Null follows cache.serializable_classes,
     * and allows none when that isn't set. Enums don't need to be listed.
     *
     * The dates below restore only their date and timezone, so they're safe to
     * allow. Don't add classes that hold callables or other objects, such as
     * models, collections, CarbonPeriod or CarbonInterval.
     */
    'serializable_classes' => [
        Carbon::class,
        CarbonImmutable::class,
        SupportCarbon::class,
        DateTime::class,
        DateTimeImmutable::class,
    ],

    /*
     * This class is responsible for serializing responses.
     */
    'serializer' => DefaultSerializer::class,
];

<?php

return [
    /*
     * Determine if the user cache should be enabled.
     */
    'enabled' => env('USER_CACHE_ENABLED', true),

    /*
     *  The given class will determinate if a request should be cached. The
     *  default class will cache all successful GET-requests.
     *
     *  You can provide your own class given that it implements the
     *  CacheProfile interface.
     */
    'cache_profile' => \Foxws\ModelCache\CacheProfiles\CacheAllSuccessful::class,

    /*
     * This must be the name of any store that is configured in config/cache.php.
     */
    'cache_store' => env('USER_CACHE_STORE') ?: env('CACHE_STORE', 'database'),

    /*
     * Default time-to-live for cache items in seconds.
     */
    'cache_lifetime_in_seconds' => (int) env('USER_CACHE_LIFETIME', 60 * 60 * 24 * 7),

    /*
     * This class is responsible for generating a hash for a request. This hash
     * is used to look up a cached response.
     */
    'hasher' => \Foxws\ModelCache\Hasher\DefaultHasher::class,

    /*
     * This class is responsible for serializing responses.
     */
    'serializer' => \Foxws\ModelCache\Serializers\DefaultSerializer::class,
];

<?php

use Foxws\UserCache\Serializers\DefaultSerializer;
use Foxws\UserCache\Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

uses(TestCase::class);

it('can serialize and unserialize a value', function () {
    // Instantiate a default serializer
    $customSerializer = app(DefaultSerializer::class);

    $serializedData = $customSerializer->serialize('testValue');

    assertTrue(is_string($serializedData));

    $unserializedData = $customSerializer->unserialize($serializedData);

    assertEquals('testValue', $unserializedData);
});

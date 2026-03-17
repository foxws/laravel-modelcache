<?php

use Foxws\ModelCache\Serializers\DefaultSerializer;
use Foxws\ModelCache\Tests\TestCase;

uses(TestCase::class);

it('can serialize and unserialize a value', function () {
    $customSerializer = app(DefaultSerializer::class);

    $serializedData = $customSerializer->serialize('testValue');

    expect($serializedData)->toBeString();

    expect($customSerializer->unserialize($serializedData))->toBe('testValue');
});

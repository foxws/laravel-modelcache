<?php

use Foxws\UserCache\Tests\Models\User;
use Foxws\UserCache\Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

uses(TestCase::class);

beforeEach(function () {
    $this->model = User::factory()->create();
});

it('can store value to user store', function () {
    $this->model->cacheStore('last_viewed', 'foo');

    assertEquals(
        'foo',
        $this->model->cacheStored('last_viewed')
    );
});

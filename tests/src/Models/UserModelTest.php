<?php

use Foxws\UserCache\Tests\Models\User;
use Foxws\UserCache\Tests\TestCase;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\assertEquals;

uses(TestCase::class);

beforeEach(function () {
    $this->model = User::factory()->create();

    Auth::login($this->model);
});

it('can store value to user store', function () {
    $this->model->cacheStore('cacheKey', 'cacheValue');

    assertEquals('cacheValue', $this->model->cacheStored('cacheKey'));
});

<?php

declare(strict_types=1);

namespace Foxws\ModelCache\Tests\Models;

use Foxws\ModelCache\Concerns\InteractsWithModelCache;
use Foxws\ModelCache\Tests\Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * Columns from the users table created in TestCase.
 *
 * @property int $id
 * @property string $uuid
 * @property string $email
 * @property string|null $name
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use InteractsWithModelCache;
    use Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}

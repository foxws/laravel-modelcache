<?php

declare(strict_types=1);

namespace Foxws\ModelCache\Tests\Models;

use Foxws\ModelCache\Concerns\InteractsWithModelCache;
use Foxws\ModelCache\Tests\Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Columns from the posts table created in TestCase.
 *
 * @property int $id
 * @property string $uuid
 * @property int|null $user_id
 * @property string $title
 * @property string|null $content
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Post extends Model
{
    use HasFactory;
    use InteractsWithModelCache;
    use SoftDeletes;

    protected $guarded = [];

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}

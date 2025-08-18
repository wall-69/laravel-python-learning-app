<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Sqids\Sqids;

/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $slug
 * @property int $views
 * @property string $status
 * @property string $blocks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecture newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecture newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecture query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecture whereBlocks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecture whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecture whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecture whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecture whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecture whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecture whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecture whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecture whereViews($value)
 * @mixin \Eloquent
 */
class Lecture extends Model
{
    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "category_id",
        "category_order",
        "title",
        "description",
        "slug",
        "views",
        "status",
        "blocks"
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($lecture) {
            if (!$lecture->id) {
                $sqids = new Sqids(minLength: 6);
                $lecture->id = $sqids->encode([random_int(1, 999999)]);
            }
        });
    }

    #[Scope]
    protected function search(Builder $query, string $value)
    {
        $query->where("title", "LIKE", "%$value%");
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

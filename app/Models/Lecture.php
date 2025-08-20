<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Sqids\Sqids;

/**
 * 
 *
 * @property string $id
 * @property int|null $category_id
 * @property int $category_order
 * @property string $title
 * @property string $description
 * @property string $slug
 * @property int $views
 * @property string $status
 * @property string $blocks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category|null $category
 * @method static \Database\Factories\LectureFactory factory($count = null, $state = [])
 * @method static Builder<static>|Lecture newModelQuery()
 * @method static Builder<static>|Lecture newQuery()
 * @method static Builder<static>|Lecture query()
 * @method static Builder<static>|Lecture whereBlocks($value)
 * @method static Builder<static>|Lecture whereCategoryId($value)
 * @method static Builder<static>|Lecture whereCategoryOrder($value)
 * @method static Builder<static>|Lecture whereCreatedAt($value)
 * @method static Builder<static>|Lecture whereDescription($value)
 * @method static Builder<static>|Lecture whereId($value)
 * @method static Builder<static>|Lecture whereSlug($value)
 * @method static Builder<static>|Lecture whereStatus($value)
 * @method static Builder<static>|Lecture whereTitle($value)
 * @method static Builder<static>|Lecture whereUpdatedAt($value)
 * @method static Builder<static>|Lecture whereViews($value)
 * @mixin \Eloquent
 */
class Lecture extends Model
{
    use HasFactory;

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

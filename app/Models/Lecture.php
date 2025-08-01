<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    protected $fillable = [
        "title",
        "description",
        "slug",
        "views",
        "status",
        "blocks"
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, "category_lecture")
            ->withPivot("order");
    }
}

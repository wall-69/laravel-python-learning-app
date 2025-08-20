<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Lecture> $lectures
 * @property-read int|null $lectures_count
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @method static Builder<static>|Category newModelQuery()
 * @method static Builder<static>|Category newQuery()
 * @method static Builder<static>|Category query()
 * @method static Builder<static>|Category whereCreatedAt($value)
 * @method static Builder<static>|Category whereDescription($value)
 * @method static Builder<static>|Category whereId($value)
 * @method static Builder<static>|Category whereSlug($value)
 * @method static Builder<static>|Category whereTitle($value)
 * @method static Builder<static>|Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "description",
        "slug"
    ];

    #[Scope]
    protected function search(Builder $query, string $value)
    {
        $query->where("title", "LIKE", "%$value%");
    }

    public function lectures()
    {
        return $this->hasMany(Lecture::class);
    }
}

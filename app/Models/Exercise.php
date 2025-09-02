<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

/**
 * 
 *
 * @property string $id
 * @property string $lecture_id
 * @property string $block
 * @property string $tests
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Lecture $lecture
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereBlock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereLectureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereTests($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exercise whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Exercise extends Model
{
    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = ["lecture_id", "block", "tests"];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($exercise) {
            if (!$exercise->id) {
                $exercise->id = (string) Str::uuid();
            }
        });
    }

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }
}

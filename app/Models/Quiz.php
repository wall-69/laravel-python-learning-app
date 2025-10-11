<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

/**
 * 
 *
 * @property string $id
 * @property string $lecture_id
 * @property object $block
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Lecture $lecture
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereBlock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereLectureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Quiz extends Model
{
    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = ["lecture_id", "block"];
    protected $casts = [
        "block" => "object",
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($quiz) {
            if (!$quiz->id) {
                $quiz->id = (string) Str::uuid();
            }
        });
    }

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }
}

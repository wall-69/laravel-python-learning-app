<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $exercise_id
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCompletion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCompletion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCompletion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCompletion whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCompletion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCompletion whereExerciseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCompletion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCompletion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExerciseCompletion whereUserId($value)
 * @mixin \Eloquent
 */
class ExerciseCompletion extends Model
{
    protected $fillable = [
        "user_id",
        "exercise_id",
        "code"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}

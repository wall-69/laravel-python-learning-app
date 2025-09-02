<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $quiz_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Quiz $quiz
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizCompletion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizCompletion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizCompletion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizCompletion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizCompletion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizCompletion whereQuizId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizCompletion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuizCompletion whereUserId($value)
 * @mixin \Eloquent
 */
class QuizCompletion extends Model
{
    protected $fillable = [
        "user_id",
        "quiz_id"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}

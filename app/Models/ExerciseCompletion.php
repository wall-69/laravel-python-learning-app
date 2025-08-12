<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $level
 * @property int $points
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProgress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProgress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProgress query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProgress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProgress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProgress whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProgress wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProgress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProgress whereUserId($value)
 * @mixin \Eloquent
 */
class UserProgress extends Model
{
    protected $fillable = [
        "user_id",
        "level",
        "points"
    ];
}

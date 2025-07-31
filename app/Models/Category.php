<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        "title",
        "description",
        "slug"
    ];

    public function lectures()
    {
        return $this->belongsToMany(Lecture::class, "category_lecture")
            ->withPivot("order")
            ->orderByRaw("pivot_order IS NULL, pivot_order ASC");
    }
}

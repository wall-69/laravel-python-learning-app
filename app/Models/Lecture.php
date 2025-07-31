<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

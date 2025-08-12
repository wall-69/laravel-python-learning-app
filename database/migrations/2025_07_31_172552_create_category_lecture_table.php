<?php

use App\Models\Category;
use App\Models\Lecture;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_lecture', function (Blueprint $table) {
            $table->foreignIdFor(Category::class)->constrained()->cascadeOnDelete();
            $table->string("lecture_id", 6);
            $table->unsignedSmallInteger("order")->nullable();
            $table->timestamps();

            $table->primary(["category_id", "lecture_id"]);
            $table->foreign("lecture_id")->references("id")->on("lectures")->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_lecture');
    }
};

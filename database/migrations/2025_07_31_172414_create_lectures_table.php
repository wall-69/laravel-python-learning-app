<?php

use App\Enums\LectureStatus;
use App\Models\Category;
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
        Schema::create('lectures', function (Blueprint $table) {
            $table->string("id", 6)->primary();
            $table->foreignIdFor(Category::class)->nullable()->nullOnDelete();
            $table->unsignedTinyInteger("category_order");
            $table->string("title");
            $table->string("description");
            $table->string("slug")->unique();
            $table->string("thumbnail")->nullable();
            $table->unsignedInteger("views")->default(0);
            $table->enum("status", LectureStatus::values());
            $table->json("blocks");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lectures');
    }
};

<?php

use App\Enums\LectureStatus;
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
            $table->string("title");
            $table->string("description");
            $table->string("slug")->unique();
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

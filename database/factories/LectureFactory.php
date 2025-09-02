<?php

namespace Database\Factories;

use App\Enums\LectureStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lecture>
 */
class LectureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            "category_id" => 1,
            "category_order" => 1,
            "title" => $title,
            "description" => fake()->sentence(),
            "slug" => function ($attributes) {
                return Str::slug($attributes["title"]);
            },
            "status" => LectureStatus::PUBLIC->value,
            "blocks" => json_encode([
                "blocks" => []
            ])
        ];
    }

    public function inCategory(int $categoryId, int $categoryOrder = 1)
    {
        return $this->state(fn($attributes) => [
            "category_id" => $categoryId,
            "category_order" => $categoryOrder
        ]);
    }
}

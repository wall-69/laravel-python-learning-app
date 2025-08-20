<?php

use App\Enums\LectureStatus;
use App\Models\Category;
use App\Models\Lecture;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertEquals;

test("viewing lecture with invalid slug redirects to correct one", function () {
    $lecture = Lecture::factory()->create();

    $response = get(route("lectures.show", $lecture));

    $response->assertMovedPermanently();
});

test("viewing lecture adds a view", function () {
    $views = random_int(0, 500);
    $lecture = Lecture::factory()->create([
        "views" => $views
    ]);

    $response = get(route("lectures.show", [$lecture, $lecture->slug]));

    $lecture->refresh();

    assertArrayHasKey($lecture->id, session("viewed_lectures", []));
    assertEquals($views + 1, $lecture->views);
});

test("creating a lecture sets valid category order", function () {
    $category = Category::factory()->create();

    $lectureWithOrderOneLess = Lecture::factory()->inCategory($category->id, 1)->create();
    $lectureWithSameOrder = Lecture::factory()->inCategory($category->id, 2)->create();
    $lectureWithOrderOneMore = Lecture::factory()->inCategory($category->id, 3)->create();

    $admin = User::factory()->admin()->create();
    actingAs($admin);

    $title = fake()->sentence(3);
    $data = [
        "category_id" => $category->id,
        "category_order" => 2,
        "title" => $title,
        "description" => fake()->sentence(),
        "status" => LectureStatus::PUBLIC->value,
        "blocks" => json_encode([
            "blocks" => []
        ])
    ];

    $response = post(route("admin.lectures.store"), $data);
    $response->assertRedirect();

    $lecture = Lecture::where("title", $title)->first();
    $lectureWithOrderOneLess->refresh();
    $lectureWithSameOrder->refresh();
    $lectureWithOrderOneMore->refresh();

    assertEquals(1, $lectureWithOrderOneLess->category_order);
    assertEquals(2, $lecture->category_order);
    assertEquals(3, $lectureWithSameOrder->category_order);
    assertEquals(4, $lectureWithOrderOneMore->category_order);
});

test("updating a lecture sets valid category order", function () {
    $category = Category::factory()->create();

    $lecture = Lecture::factory()->inCategory($category->id, 4)->create();
    $lectureWithOrderOneLess = Lecture::factory()->inCategory($category->id, 1)->create();
    $lectureWithSameOrder = Lecture::factory()->inCategory($category->id, 2)->create();
    $lectureWithOrderOneMore = Lecture::factory()->inCategory($category->id, 3)->create();

    $admin = User::factory()->admin()->create();
    actingAs($admin);

    $data = [
        "category_id" => $lecture->category_id,
        "category_order" => 2,
        "title" => $lecture->title,
        "description" => $lecture->description,
        "status" => $lecture->status,
        "blocks" => $lecture->blocks
    ];

    $response = patch(route("admin.lectures.update", $lecture), $data);
    $response->assertRedirect();

    $lecture->refresh();
    $lectureWithOrderOneLess->refresh();
    $lectureWithSameOrder->refresh();
    $lectureWithOrderOneMore->refresh();

    assertEquals(1, $lectureWithOrderOneLess->category_order);
    assertEquals(2, $lecture->category_order);
    assertEquals(3, $lectureWithSameOrder->category_order);
    assertEquals(4, $lectureWithOrderOneMore->category_order);
});

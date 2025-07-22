<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\post;

test("guest can register", function () {
    $data = [
        "first_name" => fake()->word(),
        "last_name" => fake()->word(),
        "email" => fake()->email(),
        "password" => "testing",
        "password_confirmation" => "testing",
        "tos" => true,
    ];

    $response = post(route("users.store"), $data);

    $response->assertRedirect(route("verification.notice"));
    assertAuthenticated();
});

test("user cant register", function () {
    actingAs(User::factory()->create());

    $data = [
        "first_name" => fake()->word(),
        "last_name" => fake()->word(),
        "email" => fake()->email(),
        "password" => "testing",
        "password_confirmation" => "testing",
        "tos" => true,
    ];

    $response = post(route("users.store"), $data);

    $response->assertRedirect(route("index"));
});

test("user can login", function () {
    $user = User::factory()->create([
        "password" => Hash::make("testing")
    ]);

    $response = post(route("authenticate"), [
        "email" => $user->email,
        "password" => "testing"
    ]);

    $response->assertRedirect(route("index"));
    assertAuthenticatedAs($user);
});

test("user can logout", function () {
    actingAs(User::factory()->create());

    $response = post(route("logout"));

    $response->assertRedirect(route("index"));
    assertGuest();
});

test("guest cant logout", function () {
    $response = post(route("logout"));

    $response->assertRedirect(route("login"));
    assertGuest();
});

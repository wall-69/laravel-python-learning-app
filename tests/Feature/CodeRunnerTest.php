<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

test("code runner returns unauthorized for guest", function () {
    $response = postJson(route("code-runner.run"), [
        "code" => "print('Test')"
    ]);

    $response->assertUnauthorized();
});

test("code runner returns stdout output with good code as user", function () {
    actingAs(User::factory()->create());

    $response = postJson(route("code-runner.run"), [
        "code" => "print('Test')"
    ]);

    $response->assertStatus(200);

    $response->assertJsonStructure([
        "success",
        "output",
    ]);
    $response->assertJson([
        "success" => true,
        "output" => "Test\n"
    ]);
});

test("code runner returns stderr output with bad code as user", function () {
    actingAs(User::factory()->create());

    $response = postJson(route("code-runner.run"), [
        "code" => "prinnt('Test')"
    ]);

    $response->assertStatus(200);

    $response->assertJsonStructure([
        "success",
        "output",
    ]);
    $response->assertJsonFragment([
        "success" => false
    ]);
});

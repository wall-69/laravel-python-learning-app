<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CodeRunnerController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Proxy
if (!app()->isProduction()) {
    require __DIR__ . '/proxy.dev.php';
}

Route::get("/", function () {
    return view("index");
})->name("index");

Route::get("/editor-test", function () {
    return view("editor-test");
})->middleware("auth");

// Auth
Route::controller(AuthController::class)->group(function () {
    Route::middleware("guest")->group(function () {
        Route::get("/login", "login")->name("login");
        Route::get("/register", "register")->name("register");

        Route::post("/login", "authenticate")->name("authenticate");
    });

    Route::middleware("auth")->group(function () {
        Route::post("/logout", "logout")->name("logout");
    });
});

// Email verification
Route::controller(EmailVerificationController::class)->middleware("auth")->name("verification.")->group(function () {
    Route::get("/email/verify", "notice")->name("notice");
    Route::get("/email/verify/{id}/{hash}", "verify")->middleware("signed")->name("verify");

    Route::post("/email/verification-notification", "send")->middleware("throttle:1,3")->name("send");
});

// User
Route::controller(UserController::class)->name("users.")->group(function () {
    Route::middleware("guest")->group(function () {
        Route::post("/users", "store")->name("store");
    });
});

// CodeRunner
Route::controller(CodeRunnerController::class)->middleware(["auth", "verified"])->group(function () {
    Route::post("/code-runner", "runCode")->name("code-runner.run");
});

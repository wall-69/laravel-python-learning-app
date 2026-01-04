<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CodeRunnerController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProgressController;
use App\Http\Middleware\EnsureUserIsNotBanned;
use App\Models\Lecture;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

// Proxy
if (!app()->isProduction()) {
    require __DIR__ . '/proxy.dev.php';
}

Route::get("/", function () {
    $latestLectures = Lecture::public()->latest()->take(3)->get();

    return view("index", [
        "hideHeader" => true,
        "latestLectures" => $latestLectures
    ]);
})->name("index");

// Static pages
Route::view("/zasady-ochrany-osobnych-udajov", "privacy-policy")->name("privacyPolicy");
Route::view("/podmienky-pouzivania", "terms-of-service")->name("termsOfService");

// Internal endpoint used by the external code-runner service to validate that the request comes from an authenticated user.
// Exempt from CSRF because its called server-to-server by the code-runner.
Route::post("/internal/validate-socket", function (Request $request) {
    $internalKey = env("APP_INTERNAL_KEY");

    if (!$internalKey || $request->header("x-internal-key") != $internalKey) {
        return response()->json(["success" => false], 401);
    }

    if (!Auth::check()) {
        return response()->json(["success" => false], 401);
    }

    return response()->json(["success" => true]);
})->withoutMiddleware([VerifyCsrfToken::class]);

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
    Route::get("/profil/{first_name?}.{last_name?}.{user}", "profile")->name("profile");

    Route::middleware("guest")->group(function () {
        Route::post("/users", "store")->name("store");
    });

    Route::middleware("auth")->group(function () {
        Route::get("/profil/nastavenia", "settings")->name("settings");

        Route::patch("/users/{user}", "changePassword")->name("change-password");

        Route::delete("/users/{user}", "destroy")->name("destroy");
    });
});

// UserProgress
Route::controller(UserProgressController::class)->middleware("auth")->name("user-progress.")->group(function () {
    Route::post("/user/progress/complete/quiz/{quiz}", "completeQuiz")->name("complete-quiz");
    Route::post("/user/progress/complete/exercise/{exercise}", "completeExercise")->name("complete-exercise");
});

// Lecture
Route::controller(LectureController::class)->name("lectures.")->group(function () {
    Route::get("/lekcie", "index")->name("index");
    Route::get("/lekcia/{lecture}/{slug?}", "show")->name("show");
});

// CodeRunner
Route::controller(CodeRunnerController::class)->middleware(["auth", "verified"])->group(function () {
    Route::post("/code-runner", "runCode")->name("code-runner.run");
});

// Quiz
Route::controller(QuizController::class)->middleware(["auth", "verified"])->name("quizzes.")->group(function () {
    Route::get("/kvizy", "index")->name("index");
    Route::get("/kviz/{quiz}", "show")->name("show");
});

// Exercise
Route::controller(ExerciseController::class)->middleware(["auth", "verified"])->name("exercises.")->group(function () {
    Route::get("/cvicenia", "index")->name("index");
    Route::get("/cvicenie/{exercise}", "show")->name("show");

    Route::post("/exercise/{exercise}/submit", "submitSolution")->name("submit");
});

// Admin
Route::middleware("admin")->name("admin.")->prefix("/admin")->group(function () {
    Route::controller(AdminController::class)->group(function () {
        Route::get("/", "dashboard")->name("dashboard");

        Route::post("/img/upload", "uploadImage")->name("upload-image");

        // User ban/unban
        Route::post("/users/{user}/ban", "banUser")->name("users.ban");
        Route::post("/users/{user}/unban", "unbanUser")->name("users.unban");
    });

    Route::controller(UserController::class)->group(function () {
        Route::get("/users", "adminIndex")->name("users");
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::get("/categories", "index")->name("categories");
        Route::get("/categories/create", "create")->name("categories.create");
        Route::get("/categories/{category}/edit", "edit")->name("categories.edit");

        Route::post("/categories", "store")->name("categories.store");
        Route::patch("/categories/{category}", "update")->name("categories.update");
        Route::delete("/categories/{category}", "destroy")->name("categories.destroy");
    });

    Route::controller(LectureController::class)->group(function () {
        Route::get("/lectures", "adminIndex")->name("lectures");
        Route::get("/lectures/create", "create")->name("lectures.create");
        Route::get("/lectures/{lecture}/edit", "edit")->name("lectures.edit");


        Route::post("/lectures", "store")->name("lectures.store");
        Route::patch("/lectures/{lecture}", "update")->name("lectures.update");
        Route::patch("/lectures/{lecture}/blocks", "updateBlocks")->name("lectures.updateBlocks");
        Route::delete("/lectures/{lecture}", "destroy")->name("lectures.destroy");
    });
});

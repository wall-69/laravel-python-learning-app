<?php

use Illuminate\Support\Facades\Route;

if (!app()->isProduction()) {
    require __DIR__ . '/proxy.dev.php';
}

Route::get("/", function () {
    return view("index");
})->name("index");

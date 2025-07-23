<?php

use Illuminate\Support\Facades\Route;

// Monaco editor Vite-Laravel proxy
Route::get("/node_modules/{any}", function ($any) {
    $url = "http://localhost:5173/node_modules/{$any}";

    return response()->stream(function () use ($url) {
        // Directly stream the remote content without loading it fully into memory
        readfile($url);
    }, 200, [
        "Content-Type" => "text/javascript",
        "Cache-Control" => "public, max-age=86400"
    ]);
})->where("any", "(.*)");

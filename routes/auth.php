<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::group([
    "prefix" => "auth"
], function () {
    Route::get("/login", [AuthController::class, "signInPage"])->name("login");
    Route::post("/login", [AuthController::class, "signIn"]);
    Route::post("/sign-up", [AuthController::class, "register"])->name("register");
    Route::get("/sign-up", [AuthController::class, "signUpPage"]);
    Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth');
});

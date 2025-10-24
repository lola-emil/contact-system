<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route("contactsPage");
})->name('home')->middleware("auth");


require __DIR__ . "/auth.php";
require __DIR__ . "/contacts.php";
require __DIR__ . "/user.php";
require __DIR__ . "/notification.php";


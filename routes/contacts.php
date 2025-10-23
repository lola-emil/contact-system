<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route("contactsPage");
})->name('home')->middleware("auth");


Route::group([
    "prefix" => "contacts",
    "middleware" => "auth"
], function () {
    Route::get("/", [ContactController::class, "contactsPage"])->name("contactsPage");
    Route::get("/get-contacts", [ContactController::class, "getContacts"])->name("contactsJSON");
    Route::get("/get-contact/{id}", [ContactController::class, "getContactById"]);
    // Route::get("/search-result", [ContactController::class, "searchContact"]);
    Route::get("/get-unconfirmed-shares", [ContactController::class, "getUnconfirmedShares"]);

    Route::post("/create-contact", [ContactController::class, "createContact"]);
    Route::post("/delete-contact/{id}", [ContactController::class, "deleteContact"]);
    Route::post("/update-contact/{id}", [ContactController::class, "updateContact"]);

    Route::post("/share-contact", [ContactController::class, "shareContact"]);
    Route::post("/accept-shared-contact-status", [ContactController::class, "acceptSharedContact"]);
    Route::post("/ignore-shared-contact", [ContactController::class, "ingoreSharedContact"]);
    Route::post("/share-multiple-contacts", [ContactController::class, "shareMultiple"]);
    Route::post("/delete-multiple-contacts", [ContactController::class, "deleteMultiple"]);

    Route::post("/send-private-message", [ContactController::class, "sendPrivateMessage"]);
});

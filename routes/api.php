<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [\App\Http\Controllers\AuthController::class,'login'])->name('login');
Route::post('/register', [\App\Http\Controllers\AuthController::class,'register'])->name('register');

Route::middleware('auth:sanctum')->group(function () {

    Route::controller(\App\Http\Controllers\OrderController::class)
    ->group(function () {
        Route::get('/orders', 'index');
        Route::post('/orders', 'store');
        Route::post('/orders/{order}/cancel', 'cancel');
    });

    Route::post('/logout', [\App\Http\Controllers\AuthController::class,'logout'])->name('logout');
    Route::get('/profile', [\App\Http\Controllers\AuthController::class,'profile'])->name('profile');
});

<?php

use App\Http\Controllers\Api\OAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('auth/google', [OAuthController::class, 'redirectToGoogle']);
Route::post('auth/google/callback', [OAuthController::class, 'handleGoogleCallback']);

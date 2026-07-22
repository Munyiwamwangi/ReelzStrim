<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DramaController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HistoryController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Public browsing - no auth required (like DramaWave)
Route::get('/dramas/trending', [DramaController::class, 'trending']);
Route::get('/dramas/featured', [DramaController::class, 'featured']);
Route::get('/dramas/recent', [DramaController::class, 'recent']);
Route::get('/dramas/categories', [DramaController::class, 'categories']);
Route::get('/dramas/search', [DramaController::class, 'search']);
Route::get('/dramas/highly-rated', [DramaController::class, 'highlyRated']);
Route::get('/dramas/most-watched', [DramaController::class, 'mostWatched']);
Route::get('/dramas/recommendations', [DramaController::class, 'recommendations'])->middleware('auth:sanctum');
Route::get('/dramas', [DramaController::class, 'index']);
Route::get('/dramas/{id}', [DramaController::class, 'show']);
Route::get('/dramas/{id}/episodes', [DramaController::class, 'episodes']);
Route::get('/dramas/{id}/episodes/{episodeId}', [DramaController::class, 'episode']);

// Protected routes (user-specific actions)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{dramaId}', [FavoriteController::class, 'destroy']);
    Route::get('/favorites/check/{dramaId}', [FavoriteController::class, 'check']);

    // Watch History
    Route::get('/history', [HistoryController::class, 'index']);
    Route::post('/history', [HistoryController::class, 'store']);
    Route::delete('/history', [HistoryController::class, 'destroy']);
});

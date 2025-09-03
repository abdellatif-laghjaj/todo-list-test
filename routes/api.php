<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('profile', [AuthController::class, 'profile']);
    });
});

// Task routes - all protected by JWT authentication
Route::middleware('auth:api')->group(function () {
    Route::apiResource('tasks', TaskController::class);
    Route::get('tasks-stats', [TaskController::class, 'stats']);
});

// Broadcasting authentication
Route::middleware('auth:api')->post('/broadcasting/auth', function (Request $request) {
    return response()->json(['success' => true]);
});

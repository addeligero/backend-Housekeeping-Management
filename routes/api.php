<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // User-related routes
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/update-password', [UserController::class, 'updatePassword']);
    Route::get('/users', [UserController::class, 'index']);

    // Task routes
    Route::apiResource('tasks', TaskController::class);

    // Schedule routes
    Route::apiResource('schedules', ScheduleController::class);
    Route::get('/my-schedules', [ScheduleController::class, 'mySchedules']);
    Route::post('/schedules/{schedule}/upload-proof', [ScheduleController::class, 'uploadProof']);
    Route::get('/schedule-history', [ScheduleController::class, 'getScheduleHistory']);

});

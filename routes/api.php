<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BusStatusController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// GPS Location Update
Route::post('/driver/location', [BusStatusController::class, 'updateLocation'])->middleware('auth:sanctum');


// STATUS UPDATE
Route::post('/status', [BusStatusController::class, 'updateStatus'])->middleware('auth:sanctum');

// ✅ Public API endpoints
Route::get('/routes', [BusStatusController::class, 'getRoutes']);
Route::get('/routes/{id}/stops', [BusStatusController::class, 'getRouteStops']);
Route::get('/status/latest', [BusStatusController::class, 'getLatestStatus']);

// ✅ Admin only - User Management
// ✅ Bus Management - Public GET, Protected POST/PUT/DELETE
Route::get('/buses', [BusStatusController::class, 'getBuses']);  // ← MOVED OUTSIDE

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/buses', [BusStatusController::class, 'store']);
    Route::put('/buses/{id}', [BusStatusController::class, 'update']);
    Route::delete('/buses/{id}', [BusStatusController::class, 'destroy']);
});

// Admin only - Route Management
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/routes', [BusStatusController::class, 'storeRoute']);
    Route::put('/routes/{id}', [BusStatusController::class, 'updateRoute']);
    Route::delete('/routes/{id}', [BusStatusController::class, 'destroyRoute']);
});
// ✅ Admin only - User Management
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::put('/users/{id}/approve', [UserController::class, 'approve']);
    Route::put('/users/{id}/role', [UserController::class, 'updateRole']);
    Route::put('/users/{id}', [UserController::class, 'update']);        // ← ADD
    Route::delete('/users/{id}', [UserController::class, 'destroy']);    // ← ADD
    Route::get('/buses-list', [UserController::class, 'getBuses']);      // ← ADD
});

// Test route
Route::get('/test', function() {
    return response()->json(['message' => 'API is working!']);
});
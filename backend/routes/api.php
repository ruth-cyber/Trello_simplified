<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées (nécessitent d'être connecté)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'profile']);
    Route::put('/user', [AuthController::class, 'updateProfile']);
    // Route spécifique pour le dashboard
    Route::get('/dashboard', [ProjectController::class, 'dashboard']);
    
    // Routes automatiques pour le CRUD des projets
    Route::apiResource('projects', ProjectController::class);
});
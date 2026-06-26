<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
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

    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::get('/projects/{id}', [ProjectController::class, 'show']);
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/projects/{project}/tasks', [TaskController::class, 'getProjectTasks']);
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
    // Route pour récupérer inviter un utilisateur à un projet
    Route::post('/projects/{project}/invite', [ProjectController::class, 'invite']);
    Route::delete('/projects/{project}/remove-user/{user}', [ProjectController::class, 'removeUser']);
});
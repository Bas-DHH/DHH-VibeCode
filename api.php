<?php

use App\Http\Controllers\Api\TaskApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Task API routes
    Route::apiResource('tasks', TaskApiController::class);
    Route::post('tasks/instances/{instance}/complete', [TaskApiController::class, 'complete']);
    Route::post('tasks/instances/{instance}/reopen', [TaskApiController::class, 'reopen']);
}); 
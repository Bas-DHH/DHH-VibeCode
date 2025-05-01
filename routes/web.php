<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskInstanceController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\TaskExportController;
use App\Http\Controllers\TaskAuditLogController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\CleaningTaskController;
use App\Http\Controllers\CookingTaskController;
use App\Http\Controllers\TemperatureTaskController;
use App\Http\Controllers\GoodsReceivingTaskController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Task routes
    Route::resource('tasks', TaskController::class)->except(['show']);
    
    // Task instance routes
    Route::get('tasks/instances', [TaskInstanceController::class, 'index'])->name('tasks.instances.index');
    Route::post('tasks/instances/{instance}/complete', [TaskInstanceController::class, 'complete'])->name('tasks.instances.complete');
    Route::post('tasks/instances/{instance}/skip', [TaskInstanceController::class, 'skip'])->name('tasks.instances.skip');

    Route::post('/language', [LanguageController::class, 'update'])->name('language.update');

    Route::post('/tasks/export', [TaskExportController::class, 'export'])->name('tasks.export');

    // Task routes
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::resource('tasks', TaskController::class);
        Route::post('tasks/{task}/toggle-status', [TaskController::class, 'toggleStatus'])->name('tasks.toggle-status');
        
        // Task instance routes
        Route::get('task-instances', [TaskInstanceController::class, 'index'])->name('task-instances.index');
        Route::get('task-instances/{instance}', [TaskInstanceController::class, 'show'])->name('task-instances.show');
        Route::post('task-instances/{instance}/complete', [TaskInstanceController::class, 'complete'])->name('task-instances.complete');
        Route::post('task-instances/{instance}/reopen', [TaskInstanceController::class, 'reopen'])->name('task-instances.reopen');
        Route::get('task-instances/export', [TaskInstanceController::class, 'export'])->name('task-instances.export');
        Route::get('/tasks/export', [TaskExportController::class, 'index'])->name('tasks.export.index');
        Route::post('/tasks/export/batch', [TaskExportController::class, 'batchExport'])->name('tasks.export.batch');
        Route::get('/tasks/export/download/{filename}', [TaskExportController::class, 'download'])->name('tasks.export.download');
    });

    Route::get('/tasks/{taskInstance}/audit-log', [TaskAuditLogController::class, 'index'])
        ->name('tasks.audit-log');

    // Subscription routes
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
    Route::post('/subscriptions/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('/subscriptions/resume', [SubscriptionController::class, 'resume'])->name('subscriptions.resume');
    Route::put('/subscriptions/{plan}', [SubscriptionController::class, 'update'])->name('subscriptions.update');

    // Cleaning Tasks
    Route::post('/tasks/cleaning/complete', [CleaningTaskController::class, 'complete'])
        ->name('tasks.cleaning.complete');
        
    // Cooking Tasks
    Route::post('/tasks/cooking/complete', [CookingTaskController::class, 'complete'])
        ->name('tasks.cooking.complete');
        
    // Temperature Tasks
    Route::post('/tasks/temperature/complete', [TemperatureTaskController::class, 'complete'])
        ->name('tasks.temperature.complete');
        
    // Goods Receiving Tasks
    Route::post('/tasks/goods-receiving/complete', [GoodsReceivingTaskController::class, 'complete'])
        ->name('tasks.goods-receiving.complete');
});

// Mollie webhook route
Route::post('/webhook/mollie', [WebhookController::class, 'handleWebhook']);

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskInstanceController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\TaskExportController;
use App\Http\Controllers\TaskAuditLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\SubscriptionController;
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
    Route::middleware(['auth', 'subscription'])->group(function () {
        Route::resource('tasks', TaskController::class)->except(['show']);
        
        // Task instance routes
        Route::get('tasks/instances', [TaskInstanceController::class, 'index'])->name('tasks.instances.index');
        Route::post('tasks/instances/{instance}/complete', [TaskInstanceController::class, 'complete'])->name('tasks.instances.complete');
        Route::post('tasks/instances/{instance}/skip', [TaskInstanceController::class, 'skip'])->name('tasks.instances.skip');
    });

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

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::get('/notifications/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');

    // Report routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // User management routes
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::resource('users', UserManagementController::class);
        Route::post('users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/{user}/update-role', [UserManagementController::class, 'updateRole'])->name('users.update-role');
    });

    // Webhook routes
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::resource('webhooks', WebhookController::class)->except(['show', 'edit', 'create']);
    });

    // Subscription routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('/subscriptions/{planId}', [SubscriptionController::class, 'create'])->name('subscriptions.create');
        Route::post('/subscriptions/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::post('/subscriptions/resume', [SubscriptionController::class, 'resume'])->name('subscriptions.resume');
        Route::put('/subscriptions/{planId}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
        Route::post('/subscriptions/swap', [SubscriptionController::class, 'swap'])->name('subscriptions.swap');
    });
});

Route::post('webhooks/mollie', [WebhookController::class, 'handleWebhook'])->name('webhooks.mollie');

require __DIR__.'/auth.php'; 
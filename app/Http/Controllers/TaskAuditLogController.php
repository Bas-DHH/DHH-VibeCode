<?php

namespace App\Http\Controllers;

use App\Models\TaskInstance;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskAuditLogController extends Controller
{
    public function index(TaskInstance $taskInstance)
    {
        $this->authorize('viewAuditLog', $taskInstance);

        return Inertia::render('Tasks/AuditLog', [
            'taskInstance' => $taskInstance->load(['task', 'assignedUser']),
            'auditLogs' => $taskInstance->auditLogs()
                ->with('user')
                ->orderByDesc('created_at')
                ->paginate(20),
        ]);
    }
} 
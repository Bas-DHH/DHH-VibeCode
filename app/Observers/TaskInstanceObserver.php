<?php

namespace App\Observers;

use App\Models\TaskAuditLog;
use App\Models\TaskInstance;
use Illuminate\Support\Facades\Auth;

class TaskInstanceObserver
{
    public function updating(TaskInstance $taskInstance)
    {
        // Only track changes to completed tasks
        if (!$taskInstance->completed_at) {
            return;
        }

        // Only allow admins to edit completed tasks
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can edit completed tasks.');
        }

        // Get the changed attributes
        $oldValues = array_intersect_key($taskInstance->getOriginal(), $taskInstance->getDirty());
        $newValues = array_intersect_key($taskInstance->getDirty(), $oldValues);

        // Create audit log
        TaskAuditLog::create([
            'task_instance_id' => $taskInstance->id,
            'user_id' => Auth::id(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'action' => 'updated',
            'notes' => request('audit_notes'), // Optional notes from the admin
        ]);
    }

    public function updated(TaskInstance $taskInstance)
    {
        // If the task was just completed, log it
        if ($taskInstance->wasChanged('completed_at') && $taskInstance->completed_at) {
            TaskAuditLog::create([
                'task_instance_id' => $taskInstance->id,
                'user_id' => Auth::id(),
                'old_values' => ['completed_at' => null],
                'new_values' => ['completed_at' => $taskInstance->completed_at],
                'action' => 'completed',
                'notes' => request('completion_notes'),
            ]);
        }
    }
} 
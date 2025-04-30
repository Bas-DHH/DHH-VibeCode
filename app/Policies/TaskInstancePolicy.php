<?php

namespace App\Policies;

use App\Models\TaskInstance;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskInstancePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TaskInstance $instance): bool
    {
        return $user->business_id === $instance->task->business_id;
    }

    public function complete(User $user, TaskInstance $instance): bool
    {
        return $user->business_id === $instance->task->business_id
            && $instance->status === 'pending';
    }

    public function editCompleted(User $user, TaskInstance $instance): bool
    {
        return $user->business_id === $instance->task->business_id
            && $instance->status === 'completed'
            && ($user->isAdmin() || $user->isSuperAdmin());
    }

    public function update(User $user, TaskInstance $instance): bool
    {
        // Only allow admins to edit completed tasks
        if ($instance->completed_at) {
            return $user->isAdmin() && $user->business_id === $instance->business_id;
        }

        // For incomplete tasks, allow both admins and assigned users to edit
        return $user->isAdmin() || 
            ($user->id === $instance->assigned_user_id && 
             $user->business_id === $instance->business_id);
    }

    public function delete(User $user, TaskInstance $instance): bool
    {
        return $user->business_id === $instance->task->business_id && $user->isAdmin();
    }

    public function viewAuditLog(User $user, TaskInstance $taskInstance): bool
    {
        // Only admins can view audit logs
        return $user->isAdmin() && $user->business_id === $taskInstance->business_id;
    }
} 
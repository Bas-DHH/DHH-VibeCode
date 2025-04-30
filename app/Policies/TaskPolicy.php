<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return $user->business_id === $task->business_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    public function update(User $user, Task $task): bool
    {
        return $user->business_id === $task->business_id
            && ($user->isAdmin() || $user->isSuperAdmin());
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->business_id === $task->business_id
            && ($user->isAdmin() || $user->isSuperAdmin());
    }
} 
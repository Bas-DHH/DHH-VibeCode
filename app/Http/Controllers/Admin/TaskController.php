<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function create()
    {
        return Inertia::render('Admin/Tasks/CreateTask');
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $task = Task::create([
            'title' => $request->validated('title'),
            'category' => $request->validated('category'),
            'frequency' => $request->validated('frequency'),
            'due_date' => $request->validated('due_date'),
            'status' => 'pending',
            'business_id' => Auth::user()->business_id,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Task created successfully.');
    }
} 
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskInstance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::with(['category', 'assignedUser', 'instances'])
            ->where('business_id', Auth::user()->business_id)
            ->when($request->input('category'), function ($query, $category) {
                return $query->where('task_category_id', $category);
            })
            ->when($request->input('frequency'), function ($query, $frequency) {
                return $query->where('frequency', $frequency);
            })
            ->when($request->input('status'), function ($query, $status) {
                if ($status === 'active') {
                    return $query->where('is_active', true);
                }
                if ($status === 'inactive') {
                    return $query->where('is_active', false);
                }
                return $query;
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'filters' => $request->only(['category', 'frequency', 'status']),
            'categories' => TaskCategory::all(),
            'frequencies' => ['daily', 'weekly', 'monthly'],
        ]);
    }

    public function create()
    {
        $this->authorize('create', Task::class);

        return Inertia::render('Tasks/Create', [
            'categories' => TaskCategory::all(),
            'users' => User::where('business_id', Auth::user()->business_id)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'name_nl' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions_nl' => 'nullable|string',
            'instructions_en' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,monthly',
            'scheduled_time' => 'required|date_format:H:i',
            'day_of_week' => 'required_if:frequency,weekly|integer|between:0,6',
            'day_of_month' => 'required_if:frequency,monthly|integer|between:1,31',
            'task_category_id' => 'required|exists:task_categories,id',
            'assigned_user_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $task = Task::create([
            ...$validated,
            'business_id' => Auth::user()->business_id,
            'created_by_id' => Auth::id(),
        ]);

        return redirect()->route('tasks.index')
            ->with('success', __('Task created successfully.'));
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        $task->load(['category', 'assignedUser', 'instances' => function ($query) {
            $query->orderBy('scheduled_for', 'desc');
        }]);

        return Inertia::render('Tasks/Show', [
            'task' => $task,
        ]);
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        return Inertia::render('Tasks/Edit', [
            'task' => $task,
            'categories' => TaskCategory::all(),
            'users' => User::where('business_id', Auth::user()->business_id)->get(),
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'name_nl' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions_nl' => 'nullable|string',
            'instructions_en' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,monthly',
            'scheduled_time' => 'required|date_format:H:i',
            'day_of_week' => 'required_if:frequency,weekly|integer|between:0,6',
            'day_of_month' => 'required_if:frequency,monthly|integer|between:1,31',
            'task_category_id' => 'required|exists:task_categories,id',
            'assigned_user_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')
            ->with('success', __('Task updated successfully.'));
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', __('Task deleted successfully.'));
    }

    public function complete(TaskInstance $instance, Request $request)
    {
        $this->authorize('complete', $instance);

        $validated = $request->validate([
            'input_data' => 'required|array',
            'notes' => 'nullable|string',
        ]);

        $instance->update([
            'status' => 'completed',
            'completed_by_id' => Auth::id(),
            'completed_at' => now(),
            'input_data' => $validated['input_data'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->back()
            ->with('success', __('Task completed successfully.'));
    }

    public function editCompleted(TaskInstance $instance, Request $request)
    {
        $this->authorize('editCompleted', $instance);

        $validated = $request->validate([
            'input_data' => 'required|array',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $instance->getOriginal();
        $instance->update($validated);

        return redirect()->back()
            ->with('success', __('Task updated successfully.'));
    }

    public function toggleStatus(Task $task)
    {
        $this->authorize('update', $task);

        $task->update([
            'is_active' => !$task->is_active,
        ]);

        return redirect()->back()
            ->with('success', __('Task status updated successfully.'));
    }
} 
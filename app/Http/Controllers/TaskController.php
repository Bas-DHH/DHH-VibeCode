<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskInstance;
use App\Models\User;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Interfaces\TaskSchedulerInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TaskController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_KEY_CATEGORIES = 'task_categories';
    private const CACHE_KEY_USERS = 'business_users_';

    private TaskSchedulerInterface $taskScheduler;

    public function __construct(TaskSchedulerInterface $taskScheduler)
    {
        $this->taskScheduler = $taskScheduler;
    }

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

        $categories = Cache::remember(
            self::CACHE_KEY_CATEGORIES,
            self::CACHE_TTL,
            fn () => TaskCategory::select(['id', 'name_nl', 'name_en', 'icon', 'color'])->get()
        );

        return Inertia::render('Tasks/Index', [
            'tasks' => TaskResource::collection($tasks),
            'filters' => $request->only(['category', 'frequency', 'status']),
            'categories' => $categories,
            'frequencies' => ['daily', 'weekly', 'monthly'],
        ]);
    }

    public function create()
    {
        $this->authorize('create', Task::class);

        $users = Cache::remember(
            self::CACHE_KEY_USERS . Auth::user()->business_id,
            self::CACHE_TTL,
            fn () => User::where('business_id', Auth::user()->business_id)
                ->select(['id', 'name', 'email', 'role'])
                ->get()
        );

        return Inertia::render('Tasks/Create', [
            'categories' => TaskCategory::select(['id', 'name_nl', 'name_en', 'icon', 'color'])->get(),
            'users' => $users,
        ]);
    }

    public function store(TaskRequest $request)
    {
        try {
            DB::beginTransaction();

            $task = Task::create([
                ...$request->validated(),
                'business_id' => Auth::user()->business_id,
            ]);

            $this->taskScheduler->generateTaskInstances();

            DB::commit();

            Cache::forget(self::CACHE_KEY_CATEGORIES);
            Cache::forget(self::CACHE_KEY_USERS . Auth::user()->business_id);

            return redirect()->route('tasks.index')
                ->with('success', __('Task created successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->back()
                ->with('error', __('Failed to create task. Please try again.'));
        }
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        $task->load(['category', 'assignedUser', 'instances' => function ($query) {
            $query->orderBy('scheduled_for', 'desc')
                ->select(['id', 'task_id', 'scheduled_for', 'status', 'completed_at', 'input_data']);
        }]);

        return Inertia::render('Tasks/Show', [
            'task' => $task,
        ]);
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        $users = Cache::remember(
            self::CACHE_KEY_USERS . Auth::user()->business_id,
            self::CACHE_TTL,
            fn () => User::where('business_id', Auth::user()->business_id)
                ->select(['id', 'name', 'email', 'role'])
                ->get()
        );

        return Inertia::render('Tasks/Edit', [
            'task' => $task->load(['category', 'assignedUser']),
            'categories' => TaskCategory::select(['id', 'name_nl', 'name_en', 'icon', 'color'])->get(),
            'users' => $users,
        ]);
    }

    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        try {
            DB::beginTransaction();

            $task->update($request->validated());
            $this->taskScheduler->generateTaskInstances();

            DB::commit();

            Cache::forget(self::CACHE_KEY_CATEGORIES);
            Cache::forget(self::CACHE_KEY_USERS . Auth::user()->business_id);

            return redirect()->route('tasks.index')
                ->with('success', __('Task updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->back()
                ->with('error', __('Failed to update task. Please try again.'));
        }
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        try {
            DB::beginTransaction();

            $task->delete();

            DB::commit();

            Cache::forget(self::CACHE_KEY_CATEGORIES);
            Cache::forget(self::CACHE_KEY_USERS . Auth::user()->business_id);

            return redirect()->route('tasks.index')
                ->with('success', __('Task deleted successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->back()
                ->with('error', __('Failed to delete task. Please try again.'));
        }
    }

    public function complete(TaskInstance $instance, Request $request)
    {
        $this->authorize('complete', $instance);

        try {
            DB::beginTransaction();

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

            DB::commit();

            return redirect()->back()
                ->with('success', __('Task completed successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', __('Failed to complete task. Please try again.'));
        }
    }

    public function editCompleted(TaskInstance $instance, Request $request)
    {
        $this->authorize('editCompleted', $instance);

        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'input_data' => 'required|array',
                'notes' => 'nullable|string',
            ]);

            $oldValues = $instance->getOriginal();
            $instance->update($validated);

            DB::commit();

            return redirect()->back()
                ->with('success', __('Task updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', __('Failed to update task. Please try again.'));
        }
    }

    public function toggleStatus(Task $task)
    {
        $this->authorize('update', $task);

        try {
            DB::beginTransaction();

            $task->update([
                'is_active' => !$task->is_active,
            ]);

            DB::commit();

            Cache::forget(self::CACHE_KEY_CATEGORIES);
            Cache::forget(self::CACHE_KEY_USERS . Auth::user()->business_id);

            return redirect()->back()
                ->with('success', __('Task status updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', __('Failed to update task status. Please try again.'));
        }
    }
} 
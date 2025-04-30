<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskInstance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tasks = Task::where('business_id', Auth::user()->business_id)
            ->with(['category', 'assignedUser', 'instances'])
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
            ->paginate(10);

        return response()->json($tasks);
    }

    public function show(Task $task): JsonResponse
    {
        if ($task->business_id !== Auth::user()->business_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->load(['category', 'assignedUser', 'instances']);

        return response()->json($task);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $task = Task::create([
            ...$validator->validated(),
            'business_id' => Auth::user()->business_id,
            'created_by_id' => Auth::id(),
        ]);

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        if ($task->business_id !== Auth::user()->business_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $task->update($validator->validated());

        return response()->json($task);
    }

    public function destroy(Task $task): JsonResponse
    {
        if ($task->business_id !== Auth::user()->business_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(null, 204);
    }

    public function complete(Request $request, TaskInstance $instance): JsonResponse
    {
        if ($instance->task->business_id !== Auth::user()->business_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'input_data' => 'required|array',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $instance->update([
            'status' => 'completed',
            'completed_by_id' => Auth::id(),
            'completed_at' => now(),
            'input_data' => $validator->validated()['input_data'],
            'notes' => $validator->validated()['notes'],
        ]);

        return response()->json($instance);
    }

    public function reopen(TaskInstance $instance): JsonResponse
    {
        if ($instance->task->business_id !== Auth::user()->business_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $instance->update([
            'status' => 'pending',
            'completed_by_id' => null,
            'completed_at' => null,
            'input_data' => null,
            'notes' => null,
        ]);

        return response()->json($instance);
    }
} 
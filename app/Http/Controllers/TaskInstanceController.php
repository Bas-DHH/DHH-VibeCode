<?php

namespace App\Http\Controllers;

use App\Models\TaskInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TaskInstanceController extends Controller
{
    public function index(Request $request)
    {
        $instances = TaskInstance::with(['task.category', 'task.assignedUser', 'completedBy'])
            ->select(['id', 'task_id', 'scheduled_for', 'status', 'completed_at', 'completed_by_id'])
            ->whereHas('task', function ($query) {
                $query->where('business_id', Auth::user()->business_id);
            })
            ->when($request->input('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->input('date'), function ($query, $date) {
                return $query->whereDate('scheduled_for', $date);
            })
            ->when($request->input('overdue'), function ($query) {
                return $query->where('status', 'pending')
                    ->where('scheduled_for', '<', now());
            })
            ->orderBy('scheduled_for', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('TaskInstances/Index', [
            'instances' => $instances,
            'filters' => $request->only(['status', 'date', 'overdue']),
        ]);
    }

    public function show(TaskInstance $instance)
    {
        $this->authorize('view', $instance);

        $instance->load([
            'task.category' => function ($query) {
                $query->select(['id', 'name_nl', 'name_en', 'icon', 'color']);
            },
            'task.assignedUser' => function ($query) {
                $query->select(['id', 'name', 'email']);
            },
            'completedBy' => function ($query) {
                $query->select(['id', 'name', 'email']);
            }
        ]);

        return Inertia::render('TaskInstances/Show', [
            'instance' => $instance,
        ]);
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

    public function reopen(TaskInstance $instance)
    {
        $this->authorize('update', $instance);

        try {
            DB::beginTransaction();

            $instance->update([
                'status' => 'pending',
                'completed_by_id' => null,
                'completed_at' => null,
                'input_data' => null,
                'notes' => null,
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', __('Task reopened successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', __('Failed to reopen task. Please try again.'));
        }
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category' => 'nullable|exists:task_categories,id',
        ]);

        $instances = TaskInstance::with([
            'task.category' => function ($query) {
                $query->select(['id', 'name_nl', 'name_en']);
            },
            'task.assignedUser' => function ($query) {
                $query->select(['id', 'name', 'email']);
            },
            'completedBy' => function ($query) {
                $query->select(['id', 'name', 'email']);
            }
        ])
            ->select(['id', 'task_id', 'scheduled_for', 'status', 'completed_at', 'completed_by_id', 'input_data', 'notes'])
            ->whereHas('task', function ($query) {
                $query->where('business_id', Auth::user()->business_id);
            })
            ->whereDate('scheduled_for', '>=', $validated['start_date'])
            ->whereDate('scheduled_for', '<=', $validated['end_date'])
            ->when($validated['category'] ?? null, function ($query, $category) {
                return $query->whereHas('task', function ($q) use ($category) {
                    $q->where('task_category_id', $category);
                });
            })
            ->orderBy('scheduled_for', 'desc')
            ->get();

        $filename = 'task-history-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($instances) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Task',
                'Category',
                'Scheduled For',
                'Status',
                'Completed By',
                'Completed At',
                'Input Data',
                'Notes',
            ]);

            // Add data rows
            foreach ($instances as $instance) {
                fputcsv($file, [
                    $instance->task->title,
                    $instance->task->category->name,
                    $instance->scheduled_for->format('Y-m-d H:i'),
                    $instance->status,
                    $instance->completedBy?->name ?? '-',
                    $instance->completed_at?->format('Y-m-d H:i') ?? '-',
                    json_encode($instance->input_data),
                    $instance->notes ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 
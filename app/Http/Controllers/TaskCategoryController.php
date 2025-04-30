<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TaskCategoryController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_KEY_CATEGORIES = 'task_categories';

    public function index()
    {
        $categories = Cache::remember(
            self::CACHE_KEY_CATEGORIES,
            self::CACHE_TTL,
            fn () => TaskCategory::select(['id', 'name_nl', 'name_en', 'description_nl', 'description_en', 'icon', 'color'])
                ->where('business_id', Auth::user()->business_id)
                ->active()
                ->get()
        );

        return Inertia::render('TaskCategories/Index', [
            'categories' => $categories,
        ]);
    }

    public function show(TaskCategory $category)
    {
        $this->authorize('view', $category);

        $tasks = Task::with(['assignedUser', 'instances'])
            ->select(['id', 'title', 'name_nl', 'name_en', 'description', 'frequency', 'is_active', 'assigned_user_id'])
            ->where('task_category_id', $category->id)
            ->where('business_id', Auth::user()->business_id)
            ->active()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('TaskCategories/Show', [
            'category' => $category,
            'tasks' => $tasks,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', TaskCategory::class);

        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'name_nl' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'description_nl' => 'nullable|string',
                'description_en' => 'nullable|string',
                'icon' => 'required|string|max:50',
                'color' => 'required|string|max:7',
                'is_active' => 'boolean',
            ]);

            $category = TaskCategory::create([
                ...$validated,
                'business_id' => Auth::user()->business_id,
            ]);

            DB::commit();

            Cache::forget(self::CACHE_KEY_CATEGORIES);

            return redirect()->route('task-categories.index')
                ->with('success', __('Category created successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', __('Failed to create category. Please try again.'));
        }
    }

    public function update(Request $request, TaskCategory $category)
    {
        $this->authorize('update', $category);

        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'name_nl' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'description_nl' => 'nullable|string',
                'description_en' => 'nullable|string',
                'icon' => 'required|string|max:50',
                'color' => 'required|string|max:7',
                'is_active' => 'boolean',
            ]);

            $category->update($validated);

            DB::commit();

            Cache::forget(self::CACHE_KEY_CATEGORIES);

            return redirect()->route('task-categories.index')
                ->with('success', __('Category updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', __('Failed to update category. Please try again.'));
        }
    }

    public function destroy(TaskCategory $category)
    {
        $this->authorize('delete', $category);

        try {
            DB::beginTransaction();

            if ($category->tasks()->exists()) {
                return redirect()->back()
                    ->with('error', __('Cannot delete category with associated tasks.'));
            }

            $category->delete();

            DB::commit();

            Cache::forget(self::CACHE_KEY_CATEGORIES);

            return redirect()->route('task-categories.index')
                ->with('success', __('Category deleted successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', __('Failed to delete category. Please try again.'));
        }
    }

    public function toggleStatus(TaskCategory $category)
    {
        $this->authorize('update', $category);

        try {
            DB::beginTransaction();

            $category->update([
                'is_active' => !$category->is_active,
            ]);

            DB::commit();

            Cache::forget(self::CACHE_KEY_CATEGORIES);

            return redirect()->back()
                ->with('success', __('Category status updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', __('Failed to update category status. Please try again.'));
        }
    }
} 
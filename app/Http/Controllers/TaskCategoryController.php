<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskCategoryController extends Controller
{
    public function show($category)
    {
        // Validate category
        $validCategories = ['goods_receiving', 'temperature', 'cleaning', 'cooking', 'verification'];
        if (!in_array($category, $validCategories)) {
            abort(404);
        }

        // Get tasks for the category
        $tasks = Task::where('category', $category)
            ->where('user_id', auth()->id())
            ->orderBy('due_date', 'asc')
            ->get();

        return Inertia::render('TaskCategory', [
            'category' => $category,
            'tasks' => $tasks,
        ]);
    }
} 
<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\CookingTaskRequest;
use App\Models\CookingVerificationTask;
use App\Models\TaskCompletion;
use App\Events\TaskCompleted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CookingTaskController extends Controller
{
    /**
     * Complete a cooking task.
     *
     * @param CookingTaskRequest $request
     * @return RedirectResponse
     */
    public function complete(CookingTaskRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        try {
            DB::beginTransaction();

            $task = CookingVerificationTask::findOrFail($validated['task_id']);
            
            // Check if user has permission to complete this task
            $this->authorize('complete', $task);

            // Create task completion record
            $completion = TaskCompletion::create([
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'completed_at' => now(),
                'data' => [
                    'product_name' => $validated['product_name'],
                    'temperature' => (float) $validated['temperature'],
                    'cooking_time' => $validated['cooking_time'] ?? null,
                    'visual_check_passed' => $validated['visual_check_passed'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'corrective_action' => $validated['corrective_action'] ?? null,
                ],
                'status' => $this->determineStatus($validated, $task),
            ]);

            // Log the completion in audit trail
            activity()
                ->performedOn($task)
                ->causedBy(auth()->user())
                ->withProperties($completion->data)
                ->log('completed');

            DB::commit();

            // Fire task completed event
            event(new TaskCompleted($task, $completion));

            return redirect()
                ->back()
                ->with('success', __('Task completed successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', __('Failed to complete task. Please try again.'));
        }
    }

    /**
     * Determine the completion status based on the validation data.
     *
     * @param array $validated
     * @param CookingVerificationTask $task
     * @return string
     */
    private function determineStatus(array $validated, CookingVerificationTask $task): string
    {
        // Check temperature against norm
        if ((float) $validated['temperature'] < $task->temperature_norm) {
            return 'warning';
        }

        // Check visual inspection if required
        if (isset($validated['visual_check_passed']) && !$validated['visual_check_passed']) {
            return 'warning';
        }

        // Check cooking time if required
        if ($task->cooking_time_required && empty($validated['cooking_time'])) {
            return 'warning';
        }

        return 'completed';
    }
} 
<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\CleaningTaskRequest;
use App\Models\CleaningVerificationTask;
use App\Models\TaskCompletion;
use App\Events\TaskCompleted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CleaningTaskController extends Controller
{
    /**
     * Complete a cleaning task.
     *
     * @param CleaningTaskRequest $request
     * @return RedirectResponse
     */
    public function complete(CleaningTaskRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        try {
            DB::beginTransaction();

            $task = CleaningVerificationTask::findOrFail($validated['task_id']);
            
            // Check if user has permission to complete this task
            $this->authorize('complete', $task);

            // Create task completion record
            $completion = TaskCompletion::create([
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'completed_at' => now(),
                'data' => [
                    'cleaned' => $validated['cleaned'],
                    'disinfected' => $validated['disinfected'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'corrective_action' => $validated['corrective_action'] ?? null,
                ],
                'status' => $this->determineStatus($validated),
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
     * @return string
     */
    private function determineStatus(array $validated): string
    {
        // If either cleaned is false or disinfected is required but false
        if (!$validated['cleaned'] || 
            (isset($validated['disinfected']) && !$validated['disinfected'])) {
            return 'warning';
        }

        return 'completed';
    }
} 
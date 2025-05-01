<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\TemperatureTaskRequest;
use App\Models\TemperatureVerificationTask;
use App\Models\TaskCompletion;
use App\Events\TaskCompleted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TemperatureTaskController extends Controller
{
    /**
     * Complete a temperature task.
     *
     * @param TemperatureTaskRequest $request
     * @return RedirectResponse
     */
    public function complete(TemperatureTaskRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        try {
            DB::beginTransaction();

            $task = TemperatureVerificationTask::findOrFail($validated['task_id']);
            
            // Check if user has permission to complete this task
            $this->authorize('complete', $task);

            // Create task completion record
            $completion = TaskCompletion::create([
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'completed_at' => now(),
                'data' => [
                    'temperature' => (float) $validated['temperature'],
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
     * @param TemperatureVerificationTask $task
     * @return string
     */
    private function determineStatus(array $validated, TemperatureVerificationTask $task): string
    {
        $temperature = (float) $validated['temperature'];
        
        // Check if temperature is within acceptable range
        if ($temperature < $task->min_temperature || $temperature > $task->max_temperature) {
            return 'warning';
        }

        return 'completed';
    }
} 
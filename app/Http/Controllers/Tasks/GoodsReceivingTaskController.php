<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\GoodsReceivingTaskRequest;
use App\Models\GoodsReceivingTask;
use App\Models\TaskCompletion;
use App\Events\TaskCompleted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class GoodsReceivingTaskController extends Controller
{
    /**
     * Complete a goods receiving task.
     *
     * @param GoodsReceivingTaskRequest $request
     * @return RedirectResponse
     */
    public function complete(GoodsReceivingTaskRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        try {
            DB::beginTransaction();

            $task = GoodsReceivingTask::findOrFail($validated['task_id']);
            
            // Check if user has permission to complete this task
            $this->authorize('complete', $task);

            // Create task completion record
            $completion = TaskCompletion::create([
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'completed_at' => now(),
                'data' => [
                    'supplier_name' => $validated['supplier_name'],
                    'product_name' => $validated['product_name'],
                    'batch_number' => $validated['batch_number'],
                    'expiry_date' => $validated['expiry_date'],
                    'temperature' => isset($validated['temperature']) ? (float) $validated['temperature'] : null,
                    'packaging_intact' => $validated['packaging_intact'],
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
     * @param GoodsReceivingTask $task
     * @return string
     */
    private function determineStatus(array $validated, GoodsReceivingTask $task): string
    {
        // Check packaging
        if (!$validated['packaging_intact']) {
            return 'warning';
        }

        // Check visual inspection if required
        if (isset($validated['visual_check_passed']) && !$validated['visual_check_passed']) {
            return 'warning';
        }

        // Check temperature if required
        if ($task->temperature_check_required && isset($validated['temperature'])) {
            $temperature = (float) $validated['temperature'];
            
            if (($task->min_temperature !== null && $temperature < $task->min_temperature) ||
                ($task->max_temperature !== null && $temperature > $task->max_temperature)) {
                return 'warning';
            }
        }

        return 'completed';
    }
} 
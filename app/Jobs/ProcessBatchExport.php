<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use SplTempFileObject;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExportReady;

class ProcessBatchExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $batchId;
    protected int $userId;

    public function __construct(string $batchId, int $userId)
    {
        $this->batchId = $batchId;
        $this->userId = $userId;
    }

    public function handle()
    {
        try {
            // Get export parameters
            $params = json_decode(Storage::get("exports/{$this->batchId}.json"), true);
            
            // Generate CSV
            $csv = Writer::createFromFileObject(new SplTempFileObject());
            $csv->setOutputBOM(Writer::BOM_UTF8);

            // Add headers
            $headers = $this->getHeaders($params['columns'] ?? null);
            $csv->insertOne($headers);

            // Process in chunks to handle large datasets
            $query = TaskInstance::where('business_id', User::find($this->userId)->business_id)
                ->whereBetween('completed_at', [
                    $params['start_date'],
                    $params['end_date']
                ])
                ->with(['task.category', 'completedBy']);

            if ($params['category_id']) {
                $query->whereHas('task', function ($q) use ($params) {
                    $q->where('category_id', $params['category_id']);
                });
            }

            $query->chunk(1000, function ($instances) use ($csv, $params) {
                foreach ($instances as $instance) {
                    $row = $this->formatTaskInstance($instance, $params['columns'] ?? null);
                    $csv->insertOne($row);
                }
            });

            // Save the CSV file
            $filename = $params['filename'];
            Storage::put("exports/{$filename}", $csv->toString());

            // Clean up the parameters file
            Storage::delete("exports/{$this->batchId}.json");

            // Send email notification
            $user = User::find($this->userId);
            Mail::to($user->email)->send(new ExportReady($filename));

        } catch (\Exception $e) {
            // Log the error
            \Log::error("Batch export failed: {$e->getMessage()}", [
                'batch_id' => $this->batchId,
                'user_id' => $this->userId,
                'error' => $e->getTraceAsString()
            ]);

            // Notify user of failure
            $user = User::find($this->userId);
            Mail::to($user->email)->send(new ExportFailed($this->batchId));
        }
    }

    private function getHeaders($selectedColumns = null)
    {
        $defaultHeaders = [
            'Task Name',
            'Category',
            'Product Name',
            'Measured Value',
            'Validation Norm',
            'Extra Questions',
            'Corrective Actions',
            'Notes',
            'Status',
            'Timestamp',
            'Completed By',
        ];

        if (!$selectedColumns) {
            return $defaultHeaders;
        }

        return array_intersect($defaultHeaders, $selectedColumns);
    }

    private function formatTaskInstance($instance, $selectedColumns = null)
    {
        $inputData = $instance->input_data ?? [];
        $defaultData = [
            $instance->task->title,
            $instance->task->category->name,
            $inputData['product_name'] ?? '',
            $inputData['measured_value'] ?? '',
            $inputData['validation_norm'] ?? '',
            $this->formatExtraQuestions($inputData),
            $inputData['corrective_actions'] ?? '',
            $instance->notes ?? '',
            $this->determineStatus($inputData),
            $instance->completed_at->format('Y-m-d H:i:s'),
            $instance->completedBy->name ?? 'Unknown User',
        ];

        if (!$selectedColumns) {
            return $defaultData;
        }

        $headers = $this->getHeaders($selectedColumns);
        $result = [];
        foreach ($headers as $header) {
            $index = array_search($header, $this->getHeaders());
            $result[] = $defaultData[$index] ?? '';
        }

        return $result;
    }

    private function formatExtraQuestions(array $inputData): string
    {
        $questions = [];
        foreach ($inputData as $key => $value) {
            if (str_starts_with($key, 'question_')) {
                $questions[] = sprintf('%s: %s', str_replace('question_', '', $key), $value);
            }
        }
        return implode('; ', $questions);
    }

    private function determineStatus(array $inputData): string
    {
        if (isset($inputData['is_within_range']) && !$inputData['is_within_range']) {
            return 'Warning';
        }
        if (isset($inputData['is_clean']) && !$inputData['is_clean']) {
            return 'Warning';
        }
        if (isset($inputData['is_damaged']) && $inputData['is_damaged']) {
            return 'Warning';
        }
        if (isset($inputData['is_expired']) && $inputData['is_expired']) {
            return 'Warning';
        }
        return 'OK';
    }
} 
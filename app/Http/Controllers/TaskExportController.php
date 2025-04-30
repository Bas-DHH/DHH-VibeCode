<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use App\Models\TaskInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use League\Csv\Writer;
use SplTempFileObject;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TaskExportController extends Controller
{
    public function index()
    {
        $categories = TaskCategory::orderBy('name')->get();
        $defaultStartDate = now()->subDays(7)->format('Y-m-d');
        $defaultEndDate = now()->format('Y-m-d');
        
        return inertia('Tasks/Export', [
            'categories' => $categories,
            'defaultStartDate' => $defaultStartDate,
            'defaultEndDate' => $defaultEndDate,
        ]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category_id' => 'nullable|exists:task_categories,id',
            'format' => 'nullable|in:csv,pdf',
            'columns' => 'nullable|array',
            'columns.*' => 'string',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $query = TaskInstance::where('business_id', auth()->user()->business_id)
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->with(['task.category', 'completedBy']);

        if ($request->category_id) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        $taskInstances = $query->get();

        // Check for large dataset
        if ($taskInstances->count() > 10000) {
            return back()->with('warning', __('The selected date range contains more than 10,000 records. Please select a smaller range or use batch export.'));
        }

        if ($taskInstances->isEmpty()) {
            return $this->generateEmptyExport($request->format ?? 'csv');
        }

        return $this->generateExport($taskInstances, $request->format ?? 'csv', $request->columns);
    }

    public function batchExport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category_id' => 'nullable|exists:task_categories,id',
            'format' => 'nullable|in:csv,pdf',
            'columns' => 'nullable|array',
            'columns.*' => 'string',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $query = TaskInstance::where('business_id', auth()->user()->business_id)
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->with(['task.category', 'completedBy']);

        if ($request->category_id) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        $totalCount = $query->count();
        
        if ($totalCount === 0) {
            return $this->generateEmptyExport($request->format ?? 'csv');
        }

        // Generate a unique batch ID
        $batchId = uniqid('batch_');
        $filename = $this->generateFilename($request->category_id, $startDate, $endDate, $batchId);
        
        // Store the export parameters for background processing
        Storage::put("exports/{$batchId}.json", json_encode([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'category_id' => $request->category_id,
            'format' => $request->format ?? 'csv',
            'columns' => $request->columns,
            'total_count' => $totalCount,
            'filename' => $filename,
        ]));

        // Dispatch the batch export job
        ProcessBatchExport::dispatch($batchId, auth()->id());

        return back()->with('success', __('Your export has been queued. You will receive an email when it is ready.'));
    }

    public function download($filename)
    {
        if (!Storage::exists("exports/{$filename}")) {
            return back()->with('error', __('The requested export file no longer exists.'));
        }

        // Check if the file is older than 24 hours
        $filePath = Storage::path("exports/{$filename}");
        if (time() - filemtime($filePath) > 86400) {
            Storage::delete("exports/{$filename}");
            return back()->with('error', __('The export file has expired. Please generate a new export.'));
        }

        return Storage::download("exports/{$filename}");
    }

    private function generateExport($taskInstances, $format, $selectedColumns = null)
    {
        if ($format === 'pdf') {
            return $this->generatePdfExport($taskInstances, $selectedColumns);
        }

        return $this->generateCsvExport($taskInstances, $selectedColumns);
    }

    private function generateCsvExport($taskInstances, $selectedColumns = null)
    {
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->setOutputBOM(Writer::BOM_UTF8);

        $headers = $this->getHeaders($selectedColumns);
        $csv->insertOne($headers);

        foreach ($taskInstances as $instance) {
            $row = $this->formatTaskInstance($instance, $selectedColumns);
            $csv->insertOne($row);
        }

        $filename = $this->generateFilename(
            $taskInstances->first()->task->category_id,
            $taskInstances->first()->completed_at,
            $taskInstances->last()->completed_at
        );

        return response()->streamDownload(
            function () use ($csv) {
                echo $csv->toString();
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    private function generatePdfExport($taskInstances, $selectedColumns = null)
    {
        $headers = $this->getHeaders($selectedColumns);
        $data = [];

        foreach ($taskInstances as $instance) {
            $row = $this->formatTaskInstance($instance, $selectedColumns);
            $data[] = array_combine($headers, $row);
        }

        $pdf = \PDF::loadView('exports.tasks', [
            'headers' => $headers,
            'data' => $data,
            'startDate' => $taskInstances->first()->completed_at->format('Y-m-d'),
            'endDate' => $taskInstances->last()->completed_at->format('Y-m-d'),
            'category' => $taskInstances->first()->task->category->name,
        ]);

        $filename = $this->generateFilename(
            $taskInstances->first()->task->category_id,
            $taskInstances->first()->completed_at,
            $taskInstances->last()->completed_at
        );

        return $pdf->download($filename);
    }

    private function generateEmptyExport($format)
    {
        if ($format === 'csv') {
            $csv = Writer::createFromFileObject(new SplTempFileObject());
            $csv->setOutputBOM(Writer::BOM_UTF8);
            $csv->insertOne(['No tasks completed in this range']);
            
            return response()->streamDownload(
                function () use ($csv) {
                    echo $csv->toString();
                },
                'empty_export.csv',
                [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="empty_export.csv"',
                ]
            );
        }

        return back()->with('info', __('No tasks were completed in the selected date range.'));
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

    private function generateFilename($categoryId, $startDate, $endDate, $batchId = null)
    {
        $category = $categoryId 
            ? TaskCategory::find($categoryId)->name 
            : 'all';
        
        $baseName = sprintf(
            'dhh_export_%s_%s_%s',
            strtolower(str_replace(' ', '_', $category)),
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        );

        if ($batchId) {
            $baseName .= '_' . $batchId;
        }

        return $baseName . '.csv';
    }
} 
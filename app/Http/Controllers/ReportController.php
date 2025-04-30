<?php

namespace App\Http\Controllers;

use App\Services\ReportingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportingService $reportingService
    ) {}

    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $report = $this->reportingService->generateComplianceReport(
            $request->user()->business_id,
            $startDate,
            $endDate
        );

        return Inertia::render('Reports/Index', [
            'report' => $report,
            'filters' => $request->only(['start_date', 'end_date']),
        ]);
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $report = $this->reportingService->generateComplianceReport(
            $request->user()->business_id,
            $startDate,
            $endDate
        );

        $filename = 'compliance-report-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($report) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Report Period',
                'Start Date',
                'End Date',
                'Total Tasks',
                'Completed Tasks',
                'Overdue Tasks',
                'Completion Rate',
                'Compliance Score',
            ]);

            // Add data row
            fputcsv($file, [
                $report['period']['start'] . ' to ' . $report['period']['end'],
                $report['period']['start'],
                $report['period']['end'],
                $report['overall_stats']['total_tasks'],
                $report['overall_stats']['completed_tasks'],
                $report['overall_stats']['overdue_tasks'],
                $report['overall_stats']['completion_rate'] . '%',
                $report['compliance_score'] . '%',
            ]);

            // Add user performance section
            fputcsv($file, []);
            fputcsv($file, ['User Performance']);
            fputcsv($file, ['User', 'Completed Tasks', 'Average Completion Time (minutes)']);

            foreach ($report['user_performance'] as $user) {
                fputcsv($file, [
                    $user['user_name'],
                    $user['completed_tasks'],
                    $user['average_completion_time'],
                ]);
            }

            // Add category performance section
            fputcsv($file, []);
            fputcsv($file, ['Category Performance']);
            fputcsv($file, ['Category', 'Completed Tasks', 'Average Completion Time (minutes)']);

            foreach ($report['category_performance'] as $category) {
                fputcsv($file, [
                    $category['category_name'],
                    $category['completed_tasks'],
                    $category['average_completion_time'],
                ]);
            }

            // Add trends section
            fputcsv($file, []);
            fputcsv($file, ['Daily Trends']);
            fputcsv($file, ['Date', 'Total Tasks', 'Completed Tasks', 'Overdue Tasks']);

            foreach ($report['trends'] as $trend) {
                fputcsv($file, [
                    $trend['date'],
                    $trend['total_tasks'],
                    $trend['completed_tasks'],
                    $trend['overdue_tasks'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 
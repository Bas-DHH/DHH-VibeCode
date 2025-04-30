<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Task Summary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        .task-list {
            margin-bottom: 20px;
        }
        .task-list h3 {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .task-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .task-item:last-child {
            border-bottom: none;
        }
        .overdue {
            color: #dc3545;
        }
        .completed {
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Daily Task Summary - {{ now()->format('Y-m-d') }}</h1>
    </div>

    <div class="stats">
        <div class="stat-card">
            <h3>Total Tasks</h3>
            <div class="stat-value">{{ $stats['total_tasks'] }}</div>
        </div>
        <div class="stat-card">
            <h3>Completed</h3>
            <div class="stat-value">{{ $stats['completed_tasks'] }}</div>
        </div>
        <div class="stat-card">
            <h3>Overdue</h3>
            <div class="stat-value">{{ $stats['overdue_tasks'] }}</div>
        </div>
        <div class="stat-card">
            <h3>Pending</h3>
            <div class="stat-value">{{ $stats['pending_tasks'] }}</div>
        </div>
    </div>

    @if(count($overdueTasks) > 0)
    <div class="task-list">
        <h3>Overdue Tasks</h3>
        @foreach($overdueTasks as $task)
        <div class="task-item overdue">
            <strong>{{ $task->title }}</strong>
            <p>Category: {{ $task->category->name }}</p>
            <p>Due: {{ $task->due_date->format('Y-m-d H:i') }}</p>
        </div>
        @endforeach
    </div>
    @endif

    <div class="task-list">
        <h3>Today's Tasks</h3>
        @foreach($todayTasks as $task)
        <div class="task-item">
            <strong>{{ $task->title }}</strong>
            <p>Category: {{ $task->category->name }}</p>
            <p>Due: {{ $task->due_date->format('Y-m-d H:i') }}</p>
        </div>
        @endforeach
    </div>

    <div class="task-list">
        <h3>Recently Completed</h3>
        @foreach($completedTasks as $task)
        <div class="task-item completed">
            <strong>{{ $task->title }}</strong>
            <p>Category: {{ $task->category->name }}</p>
            <p>Completed: {{ $task->completed_at->format('Y-m-d H:i') }}</p>
        </div>
        @endforeach
    </div>

    <p style="margin-top: 30px; text-align: center; color: #666;">
        This is an automated message from De Horeca Helper. Please do not reply to this email.
    </p>
</body>
</html> 
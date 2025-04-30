<?php

namespace App\Interfaces;

use App\Models\Task;
use Carbon\Carbon;

interface TaskSchedulerInterface
{
    public function generateTaskInstances(): void;
} 
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TaskSchedulerService;
use App\Services\LanguageService;
use App\Interfaces\TaskSchedulerInterface;
use App\Interfaces\LanguageServiceInterface;

class ServiceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TaskSchedulerInterface::class, TaskSchedulerService::class);
        $this->app->singleton(LanguageServiceInterface::class, LanguageService::class);
    }

    public function boot(): void
    {
        //
    }
} 
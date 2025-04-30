<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupExports extends Command
{
    protected $signature = 'exports:cleanup';
    protected $description = 'Remove export files older than 24 hours';

    public function handle()
    {
        $files = Storage::files('exports');
        $now = time();
        $deleted = 0;

        foreach ($files as $file) {
            $filePath = Storage::path($file);
            if ($now - filemtime($filePath) > 86400) { // 24 hours
                Storage::delete($file);
                $deleted++;
            }
        }

        $this->info("Cleaned up {$deleted} old export files.");
    }
} 
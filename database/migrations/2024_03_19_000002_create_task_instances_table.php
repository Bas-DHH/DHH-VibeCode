<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('completed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('scheduled_for');
            $table->enum('status', ['pending', 'completed', 'skipped'])->default('pending');
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->index(['task_id', 'scheduled_for']);
            $table->index(['status', 'scheduled_for']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_instances');
    }
}; 
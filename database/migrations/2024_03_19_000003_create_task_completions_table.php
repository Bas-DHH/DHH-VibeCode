<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_instance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('completed_by_id')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->json('input_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_completions');
    }
}; 
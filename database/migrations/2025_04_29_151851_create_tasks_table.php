<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('The name or description of the task');
            $table->enum('status', ['pending', 'done', 'overdue'])->default('pending')->comment('Current state of the task: pending, done, or overdue');
            $table->enum('category', ['temperature', 'goods_receiving', 'cooking', 'verification', 'cleaning'])->comment('The type or category of the task');
            $table->enum('frequency', ['daily', 'weekly', 'monthly'])->comment('How often the task needs to be performed');
            $table->date('due_date')->comment('The date by which the task should be completed');
            $table->timestamp('completed_at')->nullable()->comment('When the task was marked as completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

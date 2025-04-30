<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_instance_id')->constrained('task_instances')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('action'); // e.g., 'updated', 'completed', etc.
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index for faster lookups
            $table->index(['task_instance_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_audit_logs');
    }
}; 
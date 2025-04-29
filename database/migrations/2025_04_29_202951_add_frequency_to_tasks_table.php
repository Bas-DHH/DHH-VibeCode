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
        Schema::table('tasks', function (Blueprint $table) {
            $table->time('scheduled_time')->nullable(); // For specific time of day
            $table->integer('day_of_week')->nullable(); // For weekly tasks (1-7)
            $table->integer('day_of_month')->nullable(); // For monthly tasks (1-31)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['scheduled_time', 'day_of_week', 'day_of_month']);
        });
    }
};

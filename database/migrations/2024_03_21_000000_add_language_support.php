<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add language preference to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('language', 2)->default('nl');
        });

        // Add multilingual support to tasks
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('name_nl');
            $table->string('name_en')->nullable();
            $table->text('instructions_nl');
            $table->text('instructions_en')->nullable();
        });

        // Add multilingual support to task categories
        Schema::table('task_categories', function (Blueprint $table) {
            $table->string('name_nl');
            $table->string('name_en')->nullable();
            $table->text('description_nl')->nullable();
            $table->text('description_en')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('language');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['name_nl', 'name_en', 'instructions_nl', 'instructions_en']);
        });

        Schema::table('task_categories', function (Blueprint $table) {
            $table->dropColumn(['name_nl', 'name_en', 'description_nl', 'description_en']);
        });
    }
}; 
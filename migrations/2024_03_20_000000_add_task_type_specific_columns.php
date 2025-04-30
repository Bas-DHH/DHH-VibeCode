<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Temperature task columns
            $table->float('min_temperature')->nullable();
            $table->float('max_temperature')->nullable();
            $table->string('temperature_unit', 10)->nullable();
            $table->string('location')->nullable();
            $table->foreignId('equipment_id')->nullable()->constrained('equipment');

            // Goods receiving task columns
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->timestamp('delivery_time')->nullable();
            $table->json('required_documents')->nullable();
            $table->json('inspection_criteria')->nullable();

            // Cooking verification task columns
            $table->string('cooking_method')->nullable();
            $table->integer('cooking_time')->nullable();
            $table->float('internal_temperature')->nullable();
            $table->json('visual_criteria')->nullable();
            $table->json('taste_criteria')->nullable();

            // Cleaning verification task columns
            $table->string('area')->nullable();
            $table->string('cleaning_method')->nullable();
            $table->json('chemicals_used')->nullable();
            $table->json('verification_method')->nullable();
            $table->json('acceptance_criteria')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Temperature task columns
            $table->dropColumn([
                'min_temperature',
                'max_temperature',
                'temperature_unit',
                'location',
            ]);
            $table->dropForeign(['equipment_id']);
            $table->dropColumn('equipment_id');

            // Goods receiving task columns
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
            $table->dropColumn([
                'delivery_time',
                'required_documents',
                'inspection_criteria',
            ]);

            // Cooking verification task columns
            $table->dropColumn([
                'cooking_method',
                'cooking_time',
                'internal_temperature',
                'visual_criteria',
                'taste_criteria',
            ]);

            // Cleaning verification task columns
            $table->dropColumn([
                'area',
                'cleaning_method',
                'chemicals_used',
                'verification_method',
                'acceptance_criteria',
            ]);
        });
    }
}; 
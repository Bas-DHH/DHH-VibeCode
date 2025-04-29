<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('input_fields')->nullable(); // For storing custom input field configurations
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default categories
        DB::table('task_categories')->insert([
            [
                'name' => 'Temperature Control',
                'slug' => 'temperature-control',
                'description' => 'Daily temperature checks and monitoring',
                'input_fields' => json_encode([
                    'temperature' => ['type' => 'number', 'required' => true],
                    'location' => ['type' => 'text', 'required' => true],
                    'notes' => ['type' => 'textarea', 'required' => false]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Goods Receiving',
                'slug' => 'goods-receiving',
                'description' => 'Incoming goods inspection and registration',
                'input_fields' => json_encode([
                    'supplier' => ['type' => 'text', 'required' => true],
                    'temperature' => ['type' => 'number', 'required' => true],
                    'condition' => ['type' => 'select', 'required' => true, 'options' => ['good', 'acceptable', 'poor']],
                    'notes' => ['type' => 'textarea', 'required' => false]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Critical Cooking Processes',
                'slug' => 'critical-cooking',
                'description' => 'Monitoring of critical cooking temperatures and times',
                'input_fields' => json_encode([
                    'product' => ['type' => 'text', 'required' => true],
                    'temperature' => ['type' => 'number', 'required' => true],
                    'time' => ['type' => 'number', 'required' => true],
                    'notes' => ['type' => 'textarea', 'required' => false]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Verification of Measurement Devices',
                'slug' => 'measurement-devices',
                'description' => 'Regular calibration and verification of measurement equipment',
                'input_fields' => json_encode([
                    'device' => ['type' => 'text', 'required' => true],
                    'calibration_date' => ['type' => 'date', 'required' => true],
                    'next_calibration' => ['type' => 'date', 'required' => true],
                    'notes' => ['type' => 'textarea', 'required' => false]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Cleaning Records',
                'slug' => 'cleaning-records',
                'description' => 'Documentation of cleaning and sanitation procedures',
                'input_fields' => json_encode([
                    'area' => ['type' => 'text', 'required' => true],
                    'cleaning_agent' => ['type' => 'text', 'required' => true],
                    'time_completed' => ['type' => 'time', 'required' => true],
                    'notes' => ['type' => 'textarea', 'required' => false]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_categories');
    }
};

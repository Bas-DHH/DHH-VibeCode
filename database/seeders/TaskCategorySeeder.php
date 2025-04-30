<?php

namespace Database\Seeders;

use App\Models\TaskCategory;
use Illuminate\Database\Seeder;

class TaskCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Create default categories
        TaskCategory::factory()->cleaning()->create([
            'description_nl' => 'Schoonmaaktaken voor het restaurant',
            'description_en' => 'Cleaning tasks for the restaurant',
        ]);

        TaskCategory::factory()->temperature()->create([
            'description_nl' => 'Temperatuurcontroles voor koeling en vriezers',
            'description_en' => 'Temperature checks for refrigeration and freezers',
        ]);

        TaskCategory::factory()->criticalCooking()->create([
            'description_nl' => 'Kritische kookprocessen en voedselveiligheid',
            'description_en' => 'Critical cooking processes and food safety',
        ]);

        TaskCategory::factory()->goodsReceiving()->create([
            'description_nl' => 'Controle van goederen bij ontvangst',
            'description_en' => 'Goods receiving inspection',
        ]);
    }
} 
<?php

namespace Tests\Unit\Models;

use App\Models\Task;
use App\Models\TaskCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_required_fields()
    {
        $category = TaskCategory::factory()->create([
            'name' => 'Temperature Control',
            'name_nl' => 'Temperatuur Controle',
            'name_en' => 'Temperature Control',
            'description' => 'Temperature monitoring tasks',
            'description_nl' => 'Temperatuur monitoring taken',
            'description_en' => 'Temperature monitoring tasks',
            'icon' => 'thermometer',
            'color' => '#FF0000',
            'is_active' => true,
        ]);

        $this->assertEquals('Temperature Control', $category->name);
        $this->assertEquals('Temperatuur Controle', $category->name_nl);
        $this->assertEquals('Temperature Control', $category->name_en);
        $this->assertEquals('thermometer', $category->icon);
        $this->assertEquals('#FF0000', $category->color);
        $this->assertTrue($category->is_active);
    }

    public function test_category_has_tasks()
    {
        $category = TaskCategory::factory()->create();
        $task = Task::factory()->create(['task_category_id' => $category->id]);

        $this->assertTrue($category->tasks->contains($task));
    }

    public function test_category_can_be_inactive()
    {
        $category = TaskCategory::factory()->create(['is_active' => false]);
        $this->assertFalse($category->is_active);
    }

    public function test_category_requires_name()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        TaskCategory::factory()->create([
            'name' => null,
            'name_nl' => null,
            'name_en' => null,
        ]);
    }

    public function test_category_has_localized_name()
    {
        $category = TaskCategory::factory()->create([
            'name_nl' => 'Test NL',
            'name_en' => 'Test EN',
        ]);

        app()->setLocale('nl');
        $this->assertEquals('Test NL', $category->name);

        app()->setLocale('en');
        $this->assertEquals('Test EN', $category->name);
    }
} 
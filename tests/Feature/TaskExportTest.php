<?php

namespace Tests\Feature;

use App\Models\TaskCategory;
use App\Models\TaskInstance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('exports');
    }

    public function test_admin_can_export_tasks()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = TaskCategory::factory()->create();
        $taskInstance = TaskInstance::factory()->create([
            'completed_at' => now(),
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('tasks.export'), [
                'start_date' => now()->subDay()->format('Y-m-d'),
                'end_date' => now()->addDay()->format('Y-m-d'),
                'category_id' => $category->id,
                'format' => 'csv',
            ]);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_staff_cannot_export_tasks()
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)
            ->post(route('tasks.export'), [
                'start_date' => now()->subDay()->format('Y-m-d'),
                'end_date' => now()->addDay()->format('Y-m-d'),
            ]);

        $response->assertForbidden();
    }

    public function test_export_requires_valid_date_range()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->post(route('tasks.export'), [
                'start_date' => 'invalid-date',
                'end_date' => 'invalid-date',
            ]);

        $response->assertSessionHasErrors(['start_date', 'end_date']);
    }

    public function test_export_handles_empty_results()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->post(route('tasks.export'), [
                'start_date' => now()->subDay()->format('Y-m-d'),
                'end_date' => now()->addDay()->format('Y-m-d'),
            ]);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_batch_export_creates_file()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = TaskCategory::factory()->create();
        TaskInstance::factory()->count(5)->create([
            'completed_at' => now(),
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('tasks.export.batch'), [
                'start_date' => now()->subDay()->format('Y-m-d'),
                'end_date' => now()->addDay()->format('Y-m-d'),
                'category_id' => $category->id,
                'format' => 'csv',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_download_expired_export_returns_error()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $filename = 'test_export.csv';
        Storage::disk('exports')->put($filename, 'test content');
        touch(Storage::disk('exports')->path($filename), time() - 86401); // 24 hours + 1 second

        $response = $this->actingAs($admin)
            ->get(route('tasks.export.download', ['filename' => $filename]));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_cleanup_command_removes_old_files()
    {
        $filename = 'test_export.csv';
        Storage::disk('exports')->put($filename, 'test content');
        touch(Storage::disk('exports')->path($filename), time() - 86401); // 24 hours + 1 second

        $this->artisan('exports:cleanup')
            ->expectsOutput('Cleaned up 1 old export files.')
            ->assertExitCode(0);

        Storage::disk('exports')->assertMissing($filename);
    }
} 
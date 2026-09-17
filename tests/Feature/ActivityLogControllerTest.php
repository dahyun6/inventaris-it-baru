<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Barang;
use App\Models\Category;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->staff = User::factory()->staff()->create();
    }

    public function test_admin_can_view_activity_logs(): void
    {
        ActivityLogService::log('Aset', 'CREATE', 'Laptop Dell XPS', 'Menambahkan aset baru');

        $response = $this->actingAs($this->admin)->get(route('activity_logs.index'));

        $response->assertOk();
        $response->assertViewIs('admin.activity_log.index');
        $response->assertSee('Laptop Dell XPS');
    }

    public function test_staff_cannot_view_activity_logs(): void
    {
        $response = $this->actingAs($this->staff)->get(route('activity_logs.index'));

        $response->assertForbidden();
    }

    public function test_creating_category_logs_activity(): void
    {
        $response = $this->actingAs($this->admin)->post(route('category.store'), [
            'nama_kategori' => 'Smartphone Lapangan',
            'kode_prefix'   => 'HP',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'module'       => 'Kategori',
            'action'       => 'CREATE',
            'subject_name' => 'Smartphone Lapangan',
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreventiveMaintenanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_overdue_and_due_soon_helpers(): void
    {
        $overdueBarang = Barang::factory()->create([
            'interval_maintenance'       => 6,
            'tgl_maintenance_berikutnya' => Carbon::yesterday(),
        ]);

        $dueSoonBarang = Barang::factory()->create([
            'interval_maintenance'       => 6,
            'tgl_maintenance_berikutnya' => Carbon::today()->addDays(5),
        ]);

        $futureBarang = Barang::factory()->create([
            'interval_maintenance'       => 6,
            'tgl_maintenance_berikutnya' => Carbon::today()->addMonths(3),
        ]);

        $this->assertTrue($overdueBarang->isMaintenanceOverdue());
        $this->assertFalse($overdueBarang->isMaintenanceDueSoon());
        $this->assertEquals('overdue', $overdueBarang->getPreventiveStatus());

        $this->assertFalse($dueSoonBarang->isMaintenanceOverdue());
        $this->assertTrue($dueSoonBarang->isMaintenanceDueSoon(14));
        $this->assertEquals('due_soon', $dueSoonBarang->getPreventiveStatus(14));

        $this->assertFalse($futureBarang->isMaintenanceOverdue());
        $this->assertFalse($futureBarang->isMaintenanceDueSoon(14));
        $this->assertEquals('ok', $futureBarang->getPreventiveStatus(14));
    }

    public function test_can_save_barang_with_preventive_schedule(): void
    {
        $category = Category::factory()->create(['kode_prefix' => 'SRV']);
        $nextDate = Carbon::today()->addMonths(3)->format('Y-m-d');

        $response = $this->actingAs($this->user)->post(route('barang.store'), [
            'category_id'                => $category->id,
            'model'                      => 'Server Rack HP ProLiant',
            'status'                     => 'Tersedia',
            'interval_maintenance'        => 3,
            'tgl_maintenance_berikutnya' => $nextDate,
        ]);

        $response->assertRedirect(route('barang.index'));
        $this->assertDatabaseHas('barangs', [
            'model'                => 'Server Rack HP ProLiant',
            'interval_maintenance' => 3,
        ]);
    }
}

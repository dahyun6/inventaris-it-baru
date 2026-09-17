<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Category;
use App\Models\Maintenance;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_view_maintenance_index(): void
    {
        $barang = Barang::factory()->create();
        $maintenance = Maintenance::create([
            'no_maintenance'    => 'MNT-2026-0001',
            'barang_id'         => $barang->id,
            'user_id'           => $this->user->id,
            'tanggal_mulai'     => Carbon::today()->format('Y-m-d'),
            'jenis_maintenance' => 'Perbaikan Hardware',
            'pelaksana'         => 'Internal IT',
            'deskripsi_kendala' => 'Layar bergaris',
            'status'            => 'Dalam Proses',
        ]);

        $response = $this->actingAs($this->user)->get(route('maintenance.index'));

        $response->assertOk();
        $response->assertViewIs('maintenance.index');
        $response->assertSee('MNT-2026-0001');
    }

    public function test_can_export_maintenance_to_excel(): void
    {
        $barang = Barang::factory()->create();
        Maintenance::create([
            'no_maintenance'    => 'MNT-2026-0002',
            'barang_id'         => $barang->id,
            'user_id'           => $this->user->id,
            'tanggal_mulai'     => Carbon::today()->format('Y-m-d'),
            'jenis_maintenance' => 'Servis Berkala',
            'pelaksana'         => 'Internal IT',
            'deskripsi_kendala' => 'Cleaning fan & thermal paste',
            'status'            => 'Selesai',
        ]);

        $response = $this->actingAs($this->user)->get(route('maintenance.export_excel'));

        $response->assertOk();
        $this->assertTrue(
            str_contains($response->headers->get('content-disposition') ?? '', 'rekap_maintenance_')
        );
    }

    public function test_can_view_maintenance_report_print(): void
    {
        $barang = Barang::factory()->create();
        Maintenance::create([
            'no_maintenance'    => 'MNT-2026-0003',
            'barang_id'         => $barang->id,
            'user_id'           => $this->user->id,
            'tanggal_mulai'     => Carbon::today()->format('Y-m-d'),
            'jenis_maintenance' => 'Perbaikan Hardware',
            'pelaksana'         => 'Internal IT',
            'deskripsi_kendala' => 'Ganti keyboard',
            'status'            => 'Selesai',
        ]);

        $response = $this->actingAs($this->user)->get(route('maintenance.report_print'));

        $response->assertOk();
        $response->assertViewIs('maintenance.report-print');
        $response->assertSee('MNT-2026-0003');
    }

    public function test_completing_maintenance_updates_preventive_schedule_when_interval_is_set(): void
    {
        $barang = Barang::factory()->create([
            'interval_maintenance'       => 6,
            'tgl_maintenance_berikutnya' => Carbon::today(),
        ]);

        $maintenance = Maintenance::create([
            'no_maintenance'    => 'MNT-2026-0004',
            'barang_id'         => $barang->id,
            'user_id'           => $this->user->id,
            'tanggal_mulai'     => Carbon::today()->subDays(2)->format('Y-m-d'),
            'jenis_maintenance' => 'Servis Berkala',
            'pelaksana'         => 'Internal IT',
            'deskripsi_kendala' => 'Pembersihan debu & pasta',
            'status'            => 'Dalam Proses',
        ]);

        $response = $this->actingAs($this->user)->patch(route('maintenance.complete', $maintenance->id), [
            'tanggal_selesai'        => Carbon::today()->format('Y-m-d'),
            'biaya'                  => 150000,
            'tindakan_perbaikan'     => 'Pembersihan selesai dilakukan',
            'status_aset_setelahnya' => 'Tersedia',
            'nama_teknisi'           => 'Teknisi IT',
        ]);

        $response->assertRedirect(route('maintenance.show', $maintenance->id));

        $barang->refresh();
        $expectedNextDate = Carbon::today()->addMonths(6)->toDateString();
        $this->assertEquals($expectedNextDate, $barang->tgl_maintenance_berikutnya?->toDateString());
    }
}

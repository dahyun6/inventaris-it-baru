<?php

namespace Tests\Unit\Services;

use App\Models\Barang;
use App\Models\Category;
use App\Models\RiwayatAset;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DashboardService();
    }

    public function test_get_metrics_returns_accurate_counts(): void
    {
        Barang::factory()->create(['status' => 'Tersedia']);
        Barang::factory()->create(['status' => 'Dipinjam']);
        Barang::factory()->create(['status' => 'Rusak']);

        $metrics = $this->service->getMetrics();

        $this->assertEquals(3, $metrics['total_aset']);
        $this->assertEquals(1, $metrics['aset_tersedia']);
        $this->assertEquals(1, $metrics['aset_dipinjam']);
        $this->assertEquals(1, $metrics['aset_rusak']);
    }

    public function test_get_top_categories(): void
    {
        $cat1 = Category::factory()->create();
        $cat2 = Category::factory()->create();

        Barang::factory()->count(3)->create(['category_id' => $cat1->id]);
        Barang::factory()->count(1)->create(['category_id' => $cat2->id]);

        $top = $this->service->getTopCategories(2);

        $this->assertCount(2, $top);
        $this->assertEquals($cat1->id, $top->first()->id);
        $this->assertEquals(3, $top->first()->barangs_count);
    }

    public function test_get_dashboard_data(): void
    {
        $data = $this->service->getDashboardData();

        $this->assertArrayHasKey('total_aset', $data);
        $this->assertArrayHasKey('aset_tersedia', $data);
        $this->assertArrayHasKey('topCategories', $data);
        $this->assertArrayHasKey('labels_grafik', $data);
        $this->assertArrayHasKey('data_aset_masuk', $data);
    }

    public function test_get_recent_handovers_tracks_parties_and_locations(): void
    {
        $cat = Category::factory()->create();
        $barang = Barang::factory()->create(['category_id' => $cat->id, 'no_aset_local' => 'AST-TEST-01']);

        RiwayatAset::create([
            'barang_id'            => $barang->id,
            'diserahkan_oleh'      => 'Admin IT',
            'penerima_nama'        => 'User A',
            'lokasi'               => 'Ruang Finance',
            'tanggal_serah_terima' => '2026-09-01',
        ]);

        RiwayatAset::create([
            'barang_id'            => $barang->id,
            'diserahkan_oleh'      => 'User A',
            'penerima_nama'        => 'User B',
            'lokasi'               => 'Ruang Marketing',
            'tanggal_serah_terima' => '2026-09-10',
        ]);

        $recent = $this->service->getRecentHandovers(5);

        $this->assertCount(2, $recent);
        $latest = $recent->first();
        $this->assertEquals('User A', $latest->pemberi_nama);
        $this->assertEquals('User B', $latest->penerima_display);
        $this->assertEquals('Ruang Finance', $latest->lokasi_asal);
        $this->assertEquals('Ruang Marketing', $latest->lokasi);
    }
}

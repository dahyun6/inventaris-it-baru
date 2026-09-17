<?php

namespace Tests\Unit\Services;

use App\Models\LokasiUnit;
use App\Services\LokasiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LokasiServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LokasiService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LokasiService();
    }

    public function test_can_get_all_with_count(): void
    {
        LokasiUnit::factory()->count(3)->create();

        $result = $this->service->getAllWithCount();

        $this->assertCount(3, $result);
    }

    public function test_can_create_lokasi(): void
    {
        $lokasi = $this->service->create([
            'nama_lokasi' => 'Ruang Meeting Utama',
            'kode_lokasi' => 'RM-01',
            'keterangan'  => 'Lantai 1',
        ]);

        $this->assertInstanceOf(LokasiUnit::class, $lokasi);
        $this->assertDatabaseHas('lokasi_units', ['nama_lokasi' => 'Ruang Meeting Utama']);
    }

    public function test_can_update_lokasi(): void
    {
        $lokasi = LokasiUnit::factory()->create();

        $updated = $this->service->update($lokasi, [
            'nama_lokasi' => 'Ruang Direksi',
        ]);

        $this->assertEquals('Ruang Direksi', $updated->nama_lokasi);
        $this->assertDatabaseHas('lokasi_units', ['nama_lokasi' => 'Ruang Direksi']);
    }

    public function test_can_delete_lokasi(): void
    {
        $lokasi = LokasiUnit::factory()->create();

        $result = $this->service->delete($lokasi);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('lokasi_units', ['id' => $lokasi->id]);
    }
}

<?php

namespace Tests\Unit\Services;

use App\Models\Barang;
use App\Models\Category;
use App\Models\User;
use App\Services\BarangService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BarangService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BarangService();
    }

    public function test_can_create_barang_with_auto_generated_code(): void
    {
        $category = Category::factory()->create(['kode_prefix' => 'LPT']);

        $barang = $this->service->create([
            'category_id'   => $category->id,
            'model'         => 'Thinkpad X1 Carbon',
            'status'        => 'Tersedia',
            'serial_number' => 'SN-12345',
        ]);

        $this->assertInstanceOf(Barang::class, $barang);
        $this->assertEquals('LPT-001', $barang->no_aset_local);
        $this->assertDatabaseHas('barangs', [
            'id'            => $barang->id,
            'no_aset_local' => 'LPT-001',
        ]);
    }

    public function test_sequential_code_generation_for_category(): void
    {
        $category = Category::factory()->create(['kode_prefix' => 'LPT']);

        $b1 = $this->service->create([
            'category_id' => $category->id,
            'model'       => 'Model 1',
            'status'      => 'Tersedia',
        ]);

        $b2 = $this->service->create([
            'category_id' => $category->id,
            'model'       => 'Model 2',
            'status'      => 'Tersedia',
        ]);

        $this->assertEquals('LPT-001', $b1->no_aset_local);
        $this->assertEquals('LPT-002', $b2->no_aset_local);
    }

    public function test_can_update_barang(): void
    {
        $barang = Barang::factory()->create(['model' => 'Old Model']);

        $updated = $this->service->update($barang, [
            'model'  => 'New Model',
            'status' => 'Dipinjam',
        ]);

        $this->assertEquals('New Model', $updated->fresh()->model);
        $this->assertEquals('Dipinjam', $updated->fresh()->status);
    }

    public function test_can_record_handover(): void
    {
        $barang = Barang::factory()->create(['status' => 'Tersedia', 'pengguna' => null]);
        $user = User::factory()->create(['name' => 'Budi Santoso']);

        $riwayat = $this->service->recordHandover($barang, [
            'user_id'              => $user->id,
            'lokasi'               => 'Lantai 3',
            'status'               => 'Dipinjam',
            'keterangan'           => 'Peminjaman Laptop',
            'tanggal_serah_terima' => now()->toDateString(),
        ]);

        $this->assertEquals('Dipinjam', $barang->fresh()->status);
        $this->assertEquals('Budi Santoso', $barang->fresh()->pengguna);
        $this->assertEquals('Lantai 3', $barang->fresh()->unit_loc);
        $this->assertNotNull($riwayat->no_surat);
        $this->assertDatabaseHas('riwayat_asets', [
            'id'            => $riwayat->id,
            'barang_id'     => $barang->id,
            'user_id'       => $user->id,
            'penerima_nama' => 'Budi Santoso',
            'lokasi'        => 'Lantai 3',
        ]);
    }

    public function test_can_delete_barang(): void
    {
        $barang = Barang::factory()->create();

        $result = $this->service->delete($barang);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('barangs', ['id' => $barang->id]);
    }
}

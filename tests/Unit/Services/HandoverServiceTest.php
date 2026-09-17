<?php

namespace Tests\Unit\Services;

use App\Models\Barang;
use App\Models\User;
use App\Services\HandoverService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HandoverServiceTest extends TestCase
{
    use RefreshDatabase;

    protected HandoverService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HandoverService();
    }

    public function test_can_generate_no_surat(): void
    {
        $noSurat = $this->service->generateNoSurat();

        $this->assertStringStartsWith('STT/' . date('Ym') . '/', $noSurat);
    }

    public function test_can_process_multi_item_handover(): void
    {
        $user = User::factory()->create(['name' => 'Ahmad Dani']);
        $barang1 = Barang::factory()->create(['status' => 'Tersedia']);
        $barang2 = Barang::factory()->create(['status' => 'Tersedia']);

        $noSurat = 'STT/' . date('Ym') . '/0001';

        $resultNoSurat = $this->service->processHandover([
            'no_surat'             => $noSurat,
            'barang_ids'           => [$barang1->id, $barang2->id],
            'diserahkan_oleh'      => 'IT Admin',
            'penerima_nama'        => 'Ahmad Dani',
            'penerima_dept'        => 'Finance',
            'penerima_jabatan'     => 'Staff',
            'lokasi'               => 'Ruang Finance LT 2',
            'status'               => 'Dipinjam',
            'keterangan'           => 'Laptop & Monitor handover',
            'tanggal_serah_terima' => now()->toDateString(),
            'user_id'              => $user->id,
        ]);

        $this->assertEquals($noSurat, $resultNoSurat);

        $this->assertEquals('Dipinjam', $barang1->fresh()->status);
        $this->assertEquals('Ahmad Dani', $barang1->fresh()->pengguna);
        $this->assertEquals('Finance', $barang1->fresh()->dept);

        $this->assertEquals('Dipinjam', $barang2->fresh()->status);
        $this->assertEquals('Ahmad Dani', $barang2->fresh()->pengguna);

        $this->assertDatabaseHas('riwayat_asets', [
            'no_surat'      => $noSurat,
            'barang_id'     => $barang1->id,
            'penerima_nama' => 'Ahmad Dani',
        ]);

        $this->assertDatabaseHas('riwayat_asets', [
            'no_surat'      => $noSurat,
            'barang_id'     => $barang2->id,
            'penerima_nama' => 'Ahmad Dani',
        ]);
    }

    public function test_can_retrieve_receipt_items(): void
    {
        $barang = Barang::factory()->create();
        $noSurat = 'STT/' . date('Ym') . '/0099';

        $this->service->processHandover([
            'no_surat'             => $noSurat,
            'barang_ids'           => [$barang->id],
            'diserahkan_oleh'      => 'IT Admin',
            'penerima_nama'        => 'Ahmad',
            'lokasi'               => 'Gudang',
            'status'               => 'Tersedia',
            'tanggal_serah_terima' => now()->toDateString(),
        ]);

        $items = $this->service->getReceiptItems($noSurat);

        $this->assertCount(1, $items);
        $this->assertEquals($noSurat, $items->first()->no_surat);
    }
}

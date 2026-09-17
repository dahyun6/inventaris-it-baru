<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HandoverControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_view_handover_history(): void
    {
        $response = $this->actingAs($this->user)->get(route('handover.history'));

        $response->assertOk();
        $response->assertViewIs('handover.index');
    }

    public function test_can_view_handover_create_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('handover.create'));

        $response->assertOk();
        $response->assertViewIs('handover.create');
        $response->assertViewHas('autoNoSurat');
    }

    public function test_can_store_handover_and_redirect_to_receipt(): void
    {
        $barang = Barang::factory()->create(['status' => 'Tersedia']);

        $response = $this->actingAs($this->user)->post(route('handover.store'), [
            'no_surat'             => 'STT/202609/0001',
            'tanggal_serah_terima' => now()->toDateString(),
            'diserahkan_oleh'      => 'IT Officer',
            'penerima_nama'        => 'John Doe',
            'lokasi'               => 'Cabang Jakarta',
            'status'               => 'Dipinjam',
            'barang_ids'           => [$barang->id],
        ]);

        $response->assertRedirect(route('handover.receipt', ['no_surat' => urlencode('STT/202609/0001')]));
        $this->assertEquals('Dipinjam', $barang->fresh()->status);
    }

    public function test_staff_can_view_own_receipt(): void
    {
        $staff = User::factory()->staff()->create(['name' => 'Alice Staff']);
        $barang = Barang::factory()->create(['status' => 'Dipinjam', 'pengguna' => 'Alice Staff']);

        \App\Models\RiwayatAset::create([
            'barang_id'            => $barang->id,
            'user_id'              => $staff->id,
            'penerima_nama'        => 'Alice Staff',
            'no_surat'             => 'STT/202609/0099',
            'tanggal_serah_terima' => now()->toDateString(),
            'lokasi'               => 'Head Office',
            'status'               => 'Dipinjam',
        ]);

        $response = $this->actingAs($staff)->get(route('handover.receipt', 'STT/202609/0099'));

        $response->assertOk();
        $response->assertViewIs('handover.receipt');
    }

    public function test_staff_cannot_view_others_receipt(): void
    {
        $staff = User::factory()->staff()->create(['name' => 'Alice Staff']);
        $otherStaff = User::factory()->staff()->create(['name' => 'Bob Other']);
        $barang = Barang::factory()->create(['status' => 'Dipinjam', 'pengguna' => 'Bob Other']);

        \App\Models\RiwayatAset::create([
            'barang_id'            => $barang->id,
            'user_id'              => $otherStaff->id,
            'penerima_nama'        => 'Bob Other',
            'no_surat'             => 'STT/202609/0100',
            'tanggal_serah_terima' => now()->toDateString(),
            'lokasi'               => 'Head Office',
            'status'               => 'Dipinjam',
        ]);

        $response = $this->actingAs($staff)->get(route('handover.receipt', 'STT/202609/0100'));

        $response->assertForbidden();
    }
}

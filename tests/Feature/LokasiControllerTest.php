<?php

namespace Tests\Feature;

use App\Models\LokasiUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LokasiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_admin_can_view_lokasi_index(): void
    {
        $lokasi = LokasiUnit::factory()->create();

        $response = $this->actingAs($this->user)->get(route('lokasi.index'));

        $response->assertOk();
        $response->assertViewIs('lokasi.index');
        $response->assertSee($lokasi->nama_lokasi);
    }

    public function test_can_store_lokasi(): void
    {
        $response = $this->actingAs($this->user)->post(route('lokasi.store'), [
            'nama_lokasi' => 'Gedung A Lantai 2',
            'kode_lokasi' => 'GDA-02',
            'keterangan'  => 'Ruang IT and Development',
        ]);

        $response->assertRedirect(route('lokasi.index'));
        $this->assertDatabaseHas('lokasi_units', [
            'nama_lokasi' => 'Gedung A Lantai 2',
            'kode_lokasi' => 'GDA-02',
        ]);
    }

    public function test_can_update_lokasi(): void
    {
        $lokasi = LokasiUnit::factory()->create();

        $response = $this->actingAs($this->user)->put(route('lokasi.update', $lokasi), [
            'nama_lokasi' => 'Gedung B Lantai 1',
            'kode_lokasi' => 'GDB-01',
            'keterangan'  => 'Ruang Logistik',
        ]);

        $response->assertRedirect(route('lokasi.index'));
        $this->assertDatabaseHas('lokasi_units', [
            'id'          => $lokasi->id,
            'nama_lokasi' => 'Gedung B Lantai 1',
            'kode_lokasi' => 'GDB-01',
        ]);
    }

    public function test_can_delete_lokasi(): void
    {
        $lokasi = LokasiUnit::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('lokasi.destroy', $lokasi));

        $response->assertRedirect(route('lokasi.index'));
        $this->assertDatabaseMissing('lokasi_units', ['id' => $lokasi->id]);
    }
}

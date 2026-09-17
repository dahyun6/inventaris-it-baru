<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_view_barang_index(): void
    {
        $barang = Barang::factory()->create();

        $response = $this->actingAs($this->user)->get(route('barang.index'));

        $response->assertOk();
        $response->assertViewIs('barang.index');
        $response->assertSee($barang->model);
    }

    public function test_can_view_barang_create_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('barang.create'));

        $response->assertOk();
        $response->assertViewIs('barang.create');
    }

    public function test_can_store_barang(): void
    {
        $category = Category::factory()->create(['kode_prefix' => 'MON']);

        $response = $this->actingAs($this->user)->post(route('barang.store'), [
            'category_id'   => $category->id,
            'model'         => 'Monitor Dell 27 Inch 4K',
            'serial_number' => 'SN-DELL-9988',
            'status'        => 'Tersedia',
        ]);

        $response->assertRedirect(route('barang.index'));
        $this->assertDatabaseHas('barangs', [
            'model'         => 'Monitor Dell 27 Inch 4K',
            'no_aset_local' => 'MON-001',
        ]);
    }

    public function test_can_view_barang_show_page(): void
    {
        $barang = Barang::factory()->create();

        $response = $this->actingAs($this->user)->get(route('barang.show', $barang));

        $response->assertOk();
        $response->assertViewIs('barang.show');
        $response->assertSee($barang->model);
    }

    public function test_can_update_barang(): void
    {
        $category = Category::factory()->create();
        $barang = Barang::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($this->user)->put(route('barang.update', $barang), [
            'category_id'   => $category->id,
            'model'         => 'Thinkpad X1 Gen 11',
            'serial_number' => $barang->serial_number,
            'status'        => 'Dipinjam',
        ]);

        $response->assertRedirect(route('barang.index'));
        $this->assertDatabaseHas('barangs', [
            'id'    => $barang->id,
            'model' => 'Thinkpad X1 Gen 11',
        ]);
    }

    public function test_can_delete_barang(): void
    {
        $barang = Barang::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('barang.destroy', $barang));

        $response->assertRedirect(route('barang.index'));
        $this->assertDatabaseMissing('barangs', ['id' => $barang->id]);
    }

    public function test_can_generate_asset_code_via_ajax(): void
    {
        $category = Category::factory()->create(['kode_prefix' => 'SRV']);

        $response = $this->actingAs($this->user)->getJson(route('barang.generate-code', ['category_id' => $category->id]));

        $response->assertOk();
        $response->assertJson(['kode' => 'SRV-001']);
    }

    public function test_can_store_single_handover(): void
    {
        $barang = Barang::factory()->create(['status' => 'Tersedia']);

        $response = $this->actingAs($this->user)->post(route('barang.storeHandover', $barang), [
            'lokasi'               => 'Ruang IT',
            'status'               => 'Dipinjam',
            'keterangan'           => 'Dipakai dev',
            'tanggal_serah_terima' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('barang.show', $barang->uuid));
        $this->assertEquals('Dipinjam', $barang->fresh()->status);
    }

    public function test_can_download_import_template(): void
    {
        $response = $this->actingAs($this->user)->get(route('barang.template'));

        $response->assertOk();
        $response->assertHeader('content-disposition');
    }

    public function test_can_view_single_barcode_page(): void
    {
        $barang = Barang::factory()->create();

        $response = $this->actingAs($this->user)->get(route('barang.barcode', $barang->uuid));

        $response->assertOk();
        $response->assertViewIs('barang.barcode');
        $response->assertSee($barang->no_aset_local);
    }

    public function test_admin_can_view_barcode_batch_page(): void
    {
        $barang = Barang::factory()->create();

        $response = $this->actingAs($this->user)->get(route('barang.barcode.batch'));

        $response->assertOk();
        $response->assertViewIs('barang.barcode-batch');
        $response->assertSee($barang->no_aset_local);
    }

    public function test_staff_can_view_own_asset_barcode(): void
    {
        $staff = User::factory()->staff()->create(['name' => 'Budi Staff']);
        $barang = Barang::factory()->create(['pengguna' => 'Budi Staff']);

        $response = $this->actingAs($staff)->get(route('barang.barcode', $barang->uuid));

        $response->assertOk();
        $response->assertViewIs('barang.barcode');
    }

    public function test_staff_cannot_view_other_asset_barcode(): void
    {
        $staff = User::factory()->staff()->create(['name' => 'Budi Staff']);
        $otherBarang = Barang::factory()->create(['pengguna' => 'Siti Marketing']);

        $response = $this->actingAs($staff)->get(route('barang.barcode', $otherBarang->uuid));

        $response->assertForbidden();
    }

    public function test_can_view_single_qrcode_page(): void
    {
        $barang = Barang::factory()->create();

        $response = $this->actingAs($this->user)->get(route('barang.qrcode', $barang->uuid));

        $response->assertOk();
        $response->assertViewIs('barang.barcode');
        $response->assertSee($barang->no_aset_local);
    }

    public function test_admin_can_view_qrcode_batch_page(): void
    {
        $barang = Barang::factory()->create();

        $response = $this->actingAs($this->user)->get(route('barang.qrcode.batch'));

        $response->assertOk();
        $response->assertViewIs('barang.barcode-batch');
        $response->assertSee($barang->no_aset_local);
    }

    public function test_admin_can_filter_qrcode_batch_page(): void
    {
        $barang1 = Barang::factory()->create([
            'no_aset_local' => 'LP-IT-001',
            'dept'          => 'IT Dept',
            'unit_loc'      => 'Server Room',
            'status'        => 'Tersedia',
        ]);
        $barang2 = Barang::factory()->create([
            'no_aset_local' => 'PC-HR-999',
            'dept'          => 'HRD',
            'unit_loc'      => 'Lantai 2',
            'status'        => 'Rusak',
        ]);

        $response = $this->actingAs($this->user)->get(route('barang.qrcode.batch', [
            'dept'     => 'IT Dept',
            'unit_loc' => 'Server Room',
            'status'   => 'Tersedia',
            'q'        => 'LP-IT',
        ]));

        $response->assertOk();
        $response->assertSee('LP-IT-001');
        $response->assertDontSee('PC-HR-999');
    }
}

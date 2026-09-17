<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_view_vendor_index(): void
    {
        $vendor = Vendor::factory()->create();

        $response = $this->actingAs($this->user)->get(route('vendor.index'));

        $response->assertOk();
        $response->assertViewIs('vendor.index');
        $response->assertSee($vendor->nama_vendor);
    }

    public function test_can_store_vendor(): void
    {
        $response = $this->actingAs($this->user)->post(route('vendor.store'), [
            'nama_vendor' => 'PT Sumber Makmur',
            'alamat'      => 'Jakarta Pusat',
            'telepon'     => '021-5551234',
            'email'       => 'sales@sumbermakmur.com',
        ]);

        $response->assertRedirect(route('vendor.index'));
        $this->assertDatabaseHas('vendors', ['nama_vendor' => 'PT Sumber Makmur']);
    }

    public function test_can_update_vendor(): void
    {
        $vendor = Vendor::factory()->create();

        $response = $this->actingAs($this->user)->put(route('vendor.update', $vendor), [
            'nama_vendor' => 'PT Sumber Makmur Reborn',
            'alamat'      => 'Jakarta Selatan',
            'telepon'     => '021-5559999',
            'email'       => 'contact@sumbermakmur.com',
        ]);

        $response->assertRedirect(route('vendor.index'));
        $this->assertDatabaseHas('vendors', ['id' => $vendor->id, 'nama_vendor' => 'PT Sumber Makmur Reborn']);
    }

    public function test_can_delete_vendor(): void
    {
        $vendor = Vendor::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('vendor.destroy', $vendor));

        $response->assertRedirect(route('vendor.index'));
        $this->assertDatabaseMissing('vendors', ['id' => $vendor->id]);
    }
}

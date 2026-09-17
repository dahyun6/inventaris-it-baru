<?php

namespace Tests\Unit\Services;

use App\Models\Vendor;
use App\Services\VendorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected VendorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new VendorService();
    }

    public function test_can_create_vendor(): void
    {
        $vendor = $this->service->create([
            'nama_vendor' => 'PT Mitra Solusi',
            'alamat'      => 'Jl. Sudirman No 10',
            'telepon'     => '08123456789',
            'email'       => 'info@mitrasolusi.com',
        ]);

        $this->assertInstanceOf(Vendor::class, $vendor);
        $this->assertDatabaseHas('vendors', ['nama_vendor' => 'PT Mitra Solusi']);
    }

    public function test_can_update_vendor(): void
    {
        $vendor = Vendor::factory()->create(['nama_vendor' => 'Old Vendor']);

        $updated = $this->service->update($vendor, ['nama_vendor' => 'Updated Vendor']);

        $this->assertEquals('Updated Vendor', $updated->fresh()->nama_vendor);
    }

    public function test_can_delete_vendor(): void
    {
        $vendor = Vendor::factory()->create();

        $result = $this->service->delete($vendor);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('vendors', ['id' => $vendor->id]);
    }
}

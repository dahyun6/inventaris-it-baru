<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartemenControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_admin_can_view_departemen_index(): void
    {
        $dept = Departemen::factory()->create();

        $response = $this->actingAs($this->user)->get(route('departemen.index'));

        $response->assertOk();
        $response->assertViewIs('departemen.index');
        $response->assertSee($dept->nama_departemen);
    }

    public function test_can_store_departemen(): void
    {
        $response = $this->actingAs($this->user)->post(route('departemen.store'), [
            'nama_departemen' => 'Information Technology',
        ]);

        $response->assertRedirect(route('departemen.index'));
        $this->assertDatabaseHas('departemens', [
            'nama_departemen' => 'Information Technology',
        ]);
    }

    public function test_can_update_departemen(): void
    {
        $dept = Departemen::factory()->create();

        $response = $this->actingAs($this->user)->put(route('departemen.update', $dept), [
            'nama_departemen' => 'Human Resource Development',
        ]);

        $response->assertRedirect(route('departemen.index'));
        $this->assertDatabaseHas('departemens', [
            'id'              => $dept->id,
            'nama_departemen' => 'Human Resource Development',
        ]);
    }

    public function test_can_delete_departemen(): void
    {
        $dept = Departemen::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('departemen.destroy', $dept));

        $response->assertRedirect(route('departemen.index'));
        $this->assertDatabaseMissing('departemens', ['id' => $dept->id]);
    }
}

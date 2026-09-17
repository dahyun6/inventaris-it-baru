<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Departemen $departemen;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->departemen = Departemen::factory()->create();
    }

    public function test_authenticated_user_can_view_users_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('users.index'));

        $response->assertOk();
        $response->assertViewIs('admin.user.index');
        $response->assertSee($this->user->name);
    }

    public function test_can_store_user_with_departemen_and_role(): void
    {
        $response = $this->actingAs($this->user)->post(route('users.store'), [
            'name'                  => 'Rudi Hartono',
            'email'                 => 'rudi@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'departemen_id'         => $this->departemen->id,
            'role_id'               => 3,
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email'         => 'rudi@example.com',
            'departemen_id' => $this->departemen->id,
            'role_id'       => 3,
        ]);
    }

    public function test_can_update_user_with_departemen(): void
    {
        $targetUser = User::factory()->create();
        $newDept = Departemen::factory()->create();

        $response = $this->actingAs($this->user)->put(route('users.update', $targetUser), [
            'name'          => 'Rudi Updated',
            'email'         => 'rudi_updated@example.com',
            'departemen_id' => $newDept->id,
            'role_id'       => 2,
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id'            => $targetUser->id,
            'name'          => 'Rudi Updated',
            'email'         => 'rudi_updated@example.com',
            'departemen_id' => $newDept->id,
            'role_id'       => 2,
        ]);
    }

    public function test_can_delete_user(): void
    {
        $targetUser = User::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('users.destroy', $targetUser));

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }

    public function test_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->user)->delete(route('users.destroy', $this->user));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->user->id]);
    }
}

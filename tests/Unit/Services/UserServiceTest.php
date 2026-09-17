<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserService();
    }

    public function test_can_create_user(): void
    {
        $user = $this->service->create([
            'name'     => 'Budi Santoso',
            'email'    => 'budi@example.com',
            'password' => 'password123',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', ['email' => 'budi@example.com']);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_can_update_user(): void
    {
        $user = User::factory()->create([
            'name'  => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $updated = $this->service->update($user, [
            'name'     => 'New Name',
            'email'    => 'new@example.com',
            'password' => 'newpassword123',
        ]);

        $this->assertEquals('New Name', $updated->name);
        $this->assertEquals('new@example.com', $updated->email);
        $this->assertTrue(Hash::check('newpassword123', $updated->fresh()->password));
    }

    public function test_can_delete_user(): void
    {
        $user = User::factory()->create();

        $result = $this->service->delete($user);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}

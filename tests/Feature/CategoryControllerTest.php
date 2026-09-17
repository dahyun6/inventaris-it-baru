<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_view_category_index(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->get(route('category.index'));

        $response->assertOk();
        $response->assertViewIs('category.index');
        $response->assertSee($category->nama_kategori);
    }

    public function test_can_store_category(): void
    {
        $response = $this->actingAs($this->user)->post(route('category.store'), [
            'nama_kategori' => 'Networking',
            'kode_prefix'   => 'NET',
        ]);

        $response->assertRedirect(route('category.index'));
        $this->assertDatabaseHas('categories', [
            'nama_kategori' => 'Networking',
            'kode_prefix'   => 'NET',
        ]);
    }

    public function test_can_update_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->put(route('category.update', $category), [
            'nama_kategori' => 'Printer Updated',
            'kode_prefix'   => 'PRN',
        ]);

        $response->assertRedirect(route('category.index'));
        $this->assertDatabaseHas('categories', [
            'id'            => $category->id,
            'nama_kategori' => 'Printer Updated',
        ]);
    }

    public function test_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('category.destroy', $category));

        $response->assertRedirect(route('category.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
